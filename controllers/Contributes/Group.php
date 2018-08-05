<?php
namespace packages\ghafiye\controllers\contributes;
use packages\userpanel\controller;
use packages\base\{db, translator, IO\file, image, packages, inputValidation, db\duplicateRecord, db\parenthesis};
use packages\ghafiye\{view, views, authentication, group as groupObj, Contribute, contributes\groups, person};

class Group extends controller {
	protected $authentication = true;
	public function search($data) {
		$inputsRules = array(
			"word" => array(
				"type" => "string",
				"optional" => true,
				"empty" => true,
			),
		);
		$inputs = $this->checkinputs($inputsRules);
		$items = array();
		if (isset($inputs["word"])) {
			if ($inputs["word"]) {
				$groups = db::rawQuery("SELECT `ghafiye_groups`.* FROM `ghafiye_groups` INNER JOIN `ghafiye_groups_titles` ON `ghafiye_groups_titles`.`group_id` = `ghafiye_groups`.`id`
			WHERE ( `ghafiye_groups_titles`.`title` LIKE ? ) AND `ghafiye_groups`.`status` = " . groupObj::accepted . "
			UNION DISTINCT
			SELECT `ghafiye_groups`.*  FROM `ghafiye_groups` INNER JOIN `ghafiye_groups_titles` ON `ghafiye_groups_titles`.`group_id` = `ghafiye_groups`.`id` INNER JOIN `ghafiye_contributes` ON `ghafiye_contributes`.`groupID` = `ghafiye_groups`.`id`
			WHERE ( `ghafiye_groups_titles`.`title` LIKE ? )", array(
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
				));
			}
			foreach ($groups as $data) {
				$group = new groupObj($data);
				$item = $data;
				$item["name"] = $group->title();
				$items[] = $item;
			}
		}
		$this->response->setStatus(true);
		$this->response->setData(array("items" => $items));
		return $this->response;
	}
	public function add() {
		$inputsRules = array(
			"avatar" => array(
				"type" => "file",
				"optional" => true,
				"empty" => true,
			),
			"title" => array(
				"type" => "string",
			),
			"lang" => array(
				"values" => translator::$allowlangs,
			),
			"persons" => array(),
		);
		$inputs = $this->checkinputs($inputsRules);
		if (groupObj\title::byTitle($inputs["title"])) {
			throw new duplicateRecord("title");
		}
		if (person\name::byName($inputs["title"])) {
			throw new duplicateRecord("title");
		}
		if (isset($inputs["avatar"])) {
			if ($inputs["avatar"]["error"] == 0) {
				$type = getimagesize($inputs["avatar"]["tmp_name"]);
				if (!in_array($type[2], array(IMAGETYPE_JPEG , IMAGETYPE_GIF, IMAGETYPE_PNG))) {
					throw new inputValidation("image");
				}
			} else if ($inputs["avatar"]["error"] == 4) {
				unset($inputs["avatar"]);
			} else {
				throw new inputValidation("image");
			}
		}
		foreach ($inputs["persons"] as $key => $value) {
			db::join("ghafiye_contributes", "ghafiye_contributes.person=ghafiye_persons.id", "LEFT");
			$person = new person();
			$parenthesis = new parenthesis();
			$parenthesis->where("ghafiye_persons.id", $value);
			$parenthesis->where("ghafiye_persons.status", person::accepted);
			$person->orWhere($parenthesis);
			$parenthesis = new parenthesis();
			$parenthesis->where("ghafiye_contributes.user", authentication::getID());
			$parenthesis->where("ghafiye_contributes.person", $value);
			$person->orWhere($parenthesis);
			if (!$person->has()) {
				throw new inputValidation("persons[{$key}]");
			}
		}
		if (isset($inputs["avatar"])) {
			$file = new file\local($inputs["avatar"]["tmp_name"]);
			$tmpfile = new file\tmp();
			$type = getimagesize($file->getPath());
			switch($type[2]){
				case(IMAGETYPE_JPEG):
					$image = new image\jpeg($file);
					$type_name = ".jpg";
					break;
				case(IMAGETYPE_PNG):
					$image = new image\png($file);
					$type_name = ".png";
					break;
			}
			$image->saveToFile($tmpfile);
			$inputs["avatar"] = "storage/public/songs/" . $tmpfile->md5() . $type_name;
			$image = new file\local(packages::package("ghafiye")->getFilePath($inputs["avatar"]));
			$image->getDirectory()->make(true);
			$tmpfile->copyTo($image);
		}
		$group = new groupObj();
		$group->lang = $inputs["lang"];
		$group->status = groupObj::waitForAccept;
		if (isset($inputs["avatar"])) {
			$group->avatar = $inputs["avatar"];
		}
		$group->save();
		$group->title = $inputs["title"];
		$group->addTitle($inputs["title"], $inputs["lang"], groupObj\title::draft);
		foreach ($inputs["persons"] as $pid) {
			$person = new groupObj\person();
			$person->group_id = $group->id;
			$person->person = $pid;
			$person->save();
		}
		$contribute = new Contribute();
		$contribute->title = translator::trans("ghafiye.contributes.title.group.add", array("title" => $inputs["title"]));
		$contribute->user = authentication::getID();
		$contribute->groupID = $group->id;
		$contribute->type = groups\Add::class;
		$contribute->point = (new groups\Add)->getPoint();
		$contribute->save();
		$this->response->setData(array("group" => $group->toArray()));
		$this->response->setStatus(true);
		return $this->response;
	}
}
