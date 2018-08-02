<?php
namespace packages\ghafiye\controllers\contributes;
use packages\userpanel\controller;
use packages\base\{db, date, translator, IO\file, inputValidation, image, packages};
use packages\ghafiye\{view, views, authentication, song as songObj, album, person, group, Contribute, genre, contributes\songs};

class Song extends controller {
	protected $authentication = true;
	public function add($data) {
		$view = view::byName(views\contributes\songs\Add::class);
		$this->response->setView($view);
		$inputsRules = array(
			"person" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"album" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"group" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
		);
		$inputs = $this->checkinputs($inputsRules);
		if (isset($inputs["person"])) {
			if ($inputs["person"]) {
				if ($inputs["person"] = person::byId($inputs["person"])) {
					$view->setPerson($inputs["person"]);
				} else {
					unset($inputs["person"]);
				}
			} else {
				unset($inputs["person"]);
			}
		}
		if (isset($inputs["album"])) {
			if ($inputs["album"]) {
				if ($inputs["album"] = album::byId($inputs["album"])) {
					$view->setAlbum($inputs["album"]);
				} else {
					unset($inputs["album"]);
				}
			} else {
				unset($inputs["album"]);
			}
		}
		if (isset($inputs["group"])) {
			if ($inputs["group"]) {
				if ($inputs["group"] = group::byId($inputs["group"])) {
					$view->setGroup($inputs["group"]);
				} else {
					unset($inputs["group"]);
				}
			} else {
				unset($inputs["group"]);
			}
		}
		$this->response->setStatus(true);
		return $this->response;
	}
	public function store($data) {
		$view = view::byName(views\contributes\songs\Add::class);
		$this->response->setView($view);
		$allowlangs = translator::$allowlangs;
		$inputsRules = array(
			"person" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"title" => array(
				"type" => "string",
			),
			"lang" => array(
				"values" => $allowlangs,
			),
			"album" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"image" => array(
				"type" => "file",
				"optional" => true,
				"empty" => true,
			),
			"genre" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"group" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"lyrics" => array(
				"type" => "string",
			),	
		);
		$inputs = $this->checkinputs($inputsRules);
		if (isset($inputs["person"])) {
			if ($inputs["person"]) {
				if (!person::byId($inputs["person"])) {
					throw new inputValidation("person");
				}
			} else {
				unset($inputs["person"]);
			}
		}
		if (isset($inputs["album"])) {
			if ($inputs["album"]) {
				if (!album::byId($inputs["album"])) {
					throw new inputValidation("album");
				}
			} else {
				unset($inputs["album"]);
			}
		}
		if (isset($inputs["genre"])) {
			if ($inputs["genre"]) {
				if (!genre::byId($inputs["genre"])) {
					throw new inputValidation("genre");
				}
			} else {
				unset($inputs["genre"]);
			}
		}
		if (isset($inputs["group"])) {
			if ($inputs["group"]) {
				if (!group::byId($inputs["group"])) {
					throw new inputValidation("group");
				}
			} else {
				unset($inputs["group"]);
			}
		}
		if (!isset($inputs["person"]) and !isset($inputs["group"])) {
			throw new inputValidation("person");
		}
		if (isset($inputs["image"])) {
			if ($inputs["image"]["error"] == 0) {
				$type = getimagesize($inputs["image"]["tmp_name"]);
				if (!in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))) {
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
			$inputs["image"] = "/storage/public/songs/" . $tmpfile->md5() . $type_name;
			$image = new file\local(packages::package("ghafiye")->getFilePath($inputs["image"]));
			$image->getDirectory()->make(true);
			$tmpfile->copyTo($image);
		}
		$song = new songObj();
		$song->lang = $inputs["lang"];
		$song->status = songObj::draft;
		if (isset($inputs["image"])) {
			$song->image = $inputs["image"];
		}
		if (isset($inputs["genre"])) {
			$song->genre = $inputs["genre"];
		}
		if (isset($inputs["group"])) {
			$song->group = $inputs["group"];
		}
		if (isset($inputs["album"])) {
			$song->album = $inputs["album"];
		}
		$song->release_at = date::time();
		$song->duration = 0;
		$song->save();
		$song->setTitle($inputs["title"], $inputs["lang"]);
		if (isset($inputs["person"])) {
			$person = new songObj\person();
			$person->song = $song->id;
			$person->person = $inputs["person"];
			$person->primary = true;
			$person->role = songObj\person::singer;
			$person->save();
		}

		foreach (explode("\r\n", $inputs["lyrics"]) as $lyr) {
			$lyric = new songObj\lyric();
			$lyric->song = $song->id;
			$lyric->lang = $song->lang;
			$lyric->time = 0;
			$lyric->text = $lyr;
			$lyric->save();
		}

		$contribute = new Contribute();
		$contribute->title = translator::trans("ghafiye.contributes.title.songs.add", array("title" => $inputs["title"]));
		$contribute->user = authentication::getID();
		$contribute->song = $song->id;
		$contribute->type = songs\Add::class;
		$contribute->save();
		$this->response->setStatus(true);
		return $this->response;
	}
}
