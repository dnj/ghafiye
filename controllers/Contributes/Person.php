<?php
namespace packages\ghafiye\controllers\contributes;
use packages\userpanel\controller;
use packages\base\{db, translator, IO\file, image, packages, db\duplicateRecord, db\parenthesis};
use packages\ghafiye\{view, views, authentication, person as personObj, group, Contribute, contributes\persons};

class Person extends controller {
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
				$persons = db::rawQuery("SELECT DISTINCT `ghafiye_persons`.* FROM `ghafiye_persons` INNER JOIN `ghafiye_persons_names` ON `ghafiye_persons_names`.`person` = `ghafiye_persons`.`id`
			WHERE
				(
					`name_prefix` LIKE ? OR `first_name` LIKE ? OR `middle_name` LIKE ? OR `last_name` LIKE ? OR `name_suffix` LIKE ? OR `ghafiye_persons_names`.`name` LIKE ?
				) AND `ghafiye_persons`.`status` = " . personObj::accepted . "
			UNION DISTINCT
			SELECT DISTINCT `ghafiye_persons`.*  FROM `ghafiye_persons` INNER JOIN `ghafiye_persons_names` ON `ghafiye_persons_names`.`person` = `ghafiye_persons`.`id` INNER JOIN `ghafiye_contributes` ON `ghafiye_contributes`.`person` = `ghafiye_persons`.`id`
			WHERE
				(
					`name_prefix` LIKE ? OR `first_name` LIKE ? OR `middle_name` LIKE ? OR `last_name` LIKE ? OR `name_suffix` LIKE ? OR `ghafiye_persons_names`.`name` LIKE ?
				)", array(
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
				));
			}
			foreach ($persons as $data) {
				$person = new personObj($data);
				$item = $data;
				$item["name"] = $person->name();
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
			"name" => array(
				"type" => "string",
			),
			"lang" => array(
				"values" => translator::$allowlangs,
			),
			"gender" => array(
				"values" => array(personObj::men, personObj::women),
				"optional" => true,
				"empty" => true,
			),
		);
		$inputs = $this->checkinputs($inputsRules);
		if (personObj\name::byName($inputs["name"])) {
			throw new duplicateRecord("name");
		}
		if (group\title::byTitle($inputs["name"])) {
			throw new duplicateRecord("name");
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
		$person = new personObj();
		$person->status = personObj::waitForAccept;
		if (isset($inputs["avatar"])) {
			$person->avatar = $inputs["avatar"];
		}
		if (isset($inputs["gender"])) {
			$person->gender = $inputs["gender"];
		}
		$person->save();
		$person->name = $inputs["name"];
		$person->addName($inputs["name"], $inputs["lang"], personObj\name::draft);
		$contribute = new Contribute();
		$contribute->title = translator::trans("ghafiye.contributes.title.singer.add", array("name" => $inputs["name"]));
		$contribute->user = authentication::getID();
		$contribute->lang = $inputs["lang"];
		$contribute->person = $person->id;
		$contribute->type = persons\Add::class;
		$contribute->point = (new persons\Add)->getPoint();
		$contribute->save();
		$this->response->setData(array("person" => $person->toArray()));
		$this->response->setStatus(true);
		return $this->response;
	}
}
