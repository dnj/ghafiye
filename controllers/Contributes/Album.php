<?php
namespace packages\ghafiye\controllers\contributes;
use packages\userpanel\controller;
use packages\base\{db, translator, IO\file, image, packages, inputValidation, db\duplicateRecord, db\parenthesis};
use packages\ghafiye\{view, views, authentication, album as albumObj, Contribute, contributes\albums};

class Album extends controller {
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
				$albums = db::rawQuery("SELECT `ghafiye_albums`.* FROM `ghafiye_albums` INNER JOIN `ghafiye_albums_titles` ON `ghafiye_albums_titles`.`album` = `ghafiye_albums`.`id`
			WHERE ( `ghafiye_albums_titles`.`title` LIKE ? ) AND `ghafiye_albums`.`status` = " . albumObj::accepted . "
			UNION DISTINCT
			SELECT `ghafiye_albums`.*  FROM `ghafiye_albums` INNER JOIN `ghafiye_albums_titles` ON `ghafiye_albums_titles`.`album` = `ghafiye_albums`.`id` INNER JOIN `ghafiye_contributes` ON `ghafiye_contributes`.`album` = `ghafiye_albums`.`id`
			WHERE ( `ghafiye_albums_titles`.`title` LIKE ? )", array(
					'%' . $inputs["word"] . '%',
					'%' . $inputs["word"] . '%',
				));
			}
			foreach ($albums as $data) {
				$album = new albumObj($data);
				$item = $data;
				$item["name"] = $album->title();
				$items[] = $item;
			}
		}
		$this->response->setStatus(true);
		$this->response->setData(array("items" => $items));
		return $this->response;
	}
	public function add() {
		$inputsRules = array(
			"image" => array(
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
		);
		$inputs = $this->checkinputs($inputsRules);
		if (albumObj\title::byTitle($inputs["title"])) {
			throw new duplicateRecord("title");
		}
		if (isset($inputs["image"])) {
			if ($inputs["image"]["error"] == 0) {
				$type = getimagesize($inputs["image"]["tmp_name"]);
				if (!in_array($type[2], array(IMAGETYPE_JPEG , IMAGETYPE_GIF, IMAGETYPE_PNG))) {
					throw new inputValidation("image");
				}
			} else if ($inputs["image"]["error"] == 4) {
				unset($inputs["image"]);
			} else {
				throw new inputValidation("image");
			}
		}
		if (isset($inputs["image"])) {
			$file = new file\local($inputs["image"]["tmp_name"]);
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
			$inputs["image"] = "storage/public/songs/" . $tmpfile->md5() . $type_name;
			$image = new file\local(packages::package("ghafiye")->getFilePath($inputs["image"]));
			$image->getDirectory()->make(true);
			$tmpfile->copyTo($image);
		}
		$album = new albumObj();
		$album->lang = $inputs["lang"];
		$album->status = albumObj::waitForAccept;
		if (isset($inputs["image"])) {
			$album->avatar = $inputs["image"];
		}
		$album->save();
		$album->title = $inputs["title"];
		$album->addTitle($inputs["title"], $inputs["lang"], albumObj\title::draft);
		$contribute = new Contribute();
		$contribute->lang = $inputs["lang"];
		$contribute->title = translator::trans("ghafiye.contributes.title.album.add", array("title" => $inputs["title"]));
		$contribute->user = authentication::getID();
		$contribute->album = $album->id;
		$contribute->type = albums\Add::class;
		$contribute->point = (new albums\Add)->getPoint();
		$contribute->save();
		$this->response->setData(array("album" => $album->toArray()));
		$this->response->setStatus(true);
		return $this->response;
	}
}
