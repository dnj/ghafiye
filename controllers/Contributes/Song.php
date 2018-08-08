<?php
namespace packages\ghafiye\controllers\contributes;
use packages\userpanel\controller;
use packages\base\{db, date, translator, IO\file, inputValidation, image, packages, NotFound, view\error, db\parenthesis};
use packages\ghafiye\{view, views, authentication, song as songObj, album, person, group, Contribute, genre, contributes\songs, song\lyric, song\title, contribute\Lyric as ContributeLyric};

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
				db::join("ghafiye_contributes", "ghafiye_contributes.person=ghafiye_persons.id", "LEFT");
				$person = new person();
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_persons.id", $inputs["person"]);
				$parenthesis->where("ghafiye_persons.status", person::accepted);
				$person->orWhere($parenthesis);
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_contributes.user", authentication::getID());
				$parenthesis->where("ghafiye_contributes.person", $inputs["person"]);
				$person->orWhere($parenthesis);
				if (!$person->has()) {
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
				db::join("ghafiye_contributes", "ghafiye_contributes.groupID=ghafiye_groups.id", "LEFT");
				$group = new group();
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_groups.id", $inputs["group"]);
				$parenthesis->where("ghafiye_groups.status", group::accepted);
				$group->orWhere($parenthesis);
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_contributes.user", authentication::getID());
				$parenthesis->where("ghafiye_contributes.groupID", $inputs["group"]);
				$group->orWhere($parenthesis);
				if (!$group->has()) {
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
			$inputs["image"] = "storage/public/songs/" . $tmpfile->md5() . $type_name;
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
		$song->status = songObj::draft;
		$song->save();
		$song->addTitle($inputs["title"], $inputs["lang"], songObj\title::draft);
		if (isset($inputs["person"])) {
			$person = new songObj\person();
			$person->song = $song->id;
			$person->person = $inputs["person"];
			$person->primary = true;
			$person->role = songObj\person::singer;
			$person->save();
		}
		$i = 1;
		foreach (explode("\r\n", $inputs["lyrics"]) as $lyr) {
			if ($lyr) {
				$lyric = new lyric();
				$lyric->song = $song->id;
				$lyric->lang = $song->lang;
				$lyric->time = 0;
				$lyric->text = trim($lyr);
				$lyric->status = lyric::draft;
				$lyric->ordering = $i++;
				$lyric->save();
			}
		}
		
		$contribute = new Contribute();
		$contribute->title = translator::trans("ghafiye.contributes.title.songs.add", array("title" => $inputs["title"]));
		$contribute->user = authentication::getID();
		$contribute->song = $song->id;
		$contribute->lang = $song->lang;
		$contribute->type = songs\Add::class;
		$contribute->point = (new songs\Add)->getPoint();
		$contribute->save();
		$this->response->setStatus(true);
		return $this->response;
	}
	public function translate($data) {
		$song = new songObj();
		$song->where("ghafiye_songs.id", $data["song"]);
		$song->where("ghafiye_songs.status", songObj::publish);
		if (!$song = $song->getOne("ghafiye_songs.*")) {
			throw new NotFound();
		}
		$view = view::byName(views\contributes\songs\Translate::class);
		$this->response->setView($view);
		$view->setSong($song);
		$inputsRules = array(
			"songlang" => array(
				"type" => "string",
				"optional" => true,
				"empty" => true,
			),
		);
		$inputs = $this->checkinputs($inputsRules);
		if (isset($inputs["songlang"]) and $inputs["songlang"]) {
			if (in_array($inputs["songlang"], translator::$allowlangs)) {
				$lyric = new lyric();
				$lyric->where("song", $song->id);
				$lyric->where("lang", $inputs["songlang"]);
				$lyric->where("status", lyric::published);
				$view->setTranslateLyrics($lyric->get());
				$view->setTranslateTitle($song->title($inputs["songlang"]));
				$view->setTranslateLang($inputs["songlang"]);
				$contribute = new Contribute();
				$contribute->where("lang", $inputs["songlang"]);
				$contribute->where("status", Contribute::waitForAccept);
				$contribute->where("user", authentication::getID());
				$contribute->where("type", songs\Translate::class);
				if ($contribute->has()) {
					$error = new error();
					$error->setType(error::WARNING);
					$error->setCode("ghafiye.has.contribute.wait.for.accept");
					$error->setMessage(translator::trans("error.ghafiye.has.contribute.wait.for.accept"));
					$view->addError($error);
				}
			}
		}
		$this->response->setStatus(true);
		return $this->response;
	}
	public function doTranslate($data) {
		$song = new songObj();
		$song->where("ghafiye_songs.id", $data["song"]);
		$song->where("ghafiye_songs.status", songObj::publish);
		if (!$song = $song->getOne("ghafiye_songs.*")) {
			throw new NotFound();
		}
		$view = view::byName(views\contributes\songs\Translate::class);
		$this->response->setView($view);
		$view->setSong($song);
		$inputsRules = array(
			"lang" => array(
				"values" => translator::$allowlangs,
			),
			"title" => array(
				"type" => "string",
				"optional" => true,
			),
			"translates" => array(),
		);
		$inputs = $this->checkinputs($inputsRules);
		if ($inputs["lang"] == $song->lang) {
			throw new inputValidation("lang");
		}
		if (!$inputs["translates"]) {
			throw new inputValidation("translates");
		}
		$lyric = new lyric();
		$lyric->where("song", $song->id);
		$lyric->where("status", lyric::published);
		$lyric->where("lang", array($song->lang, $inputs["lang"]), "in");
		$lyrics = $lyric->get();
		foreach ($lyrics as $lyric) {
			if ($lyric->lang == $song->lang) {
				if (isset($inputs["translates"][$lyric->id])) {
					if (!$inputs["translates"][$lyric->id]) {
						unset($inputs["translates"][$lyric->id]);
					}
				}
			} else {
				if (isset($inputs["translates"][$lyric->parent])) {
					if ($inputs["translates"][$lyric->parent]) {
						if ($lyric->text == $inputs["translates"][$lyric->parent]) {
							unset($inputs["translates"][$lyric->parent]);
						}
					} else {
						unset($inputs["translates"][$lyric->parent]);
					}
				}
			}
		}
		if ($inputs["translates"] or (isset($inputs["title"]) and $song->title($inputs["lang"]) != $inputs["title"])) {
			$contribute = new Contribute();
			$contribute->title = translator::trans("ghafiye.contributes.title.songs.translate", array(
				"title" => $song->title($song->lang),
				"lang" => translator::trans("translations.langs.{$inputs["lang"]}"),
			));
			$contribute->user = authentication::getID();
			$contribute->song = $song->id;
			$contribute->lang = $inputs["lang"];
			$contribute->type = songs\Translate::class;
			$contribute->point = (new songs\Translate)->getPoint();
			$contribute->save();
			foreach ($lyrics as $lyric) {
				if ($lyric->lang == $song->lang) {
					continue;
				}
				if (isset($inputs["translates"][$lyric->parent])) {
					$clyr = new ContributeLyric();
					$clyr->contribute = $contribute->id;
					$clyr->parent = $lyric->parent;
					$clyr->lyric = $lyric->id;
					$clyr->old_text = $lyric->text;
					$clyr->text = $inputs["translates"][$lyric->parent];
					$clyr->save();
					unset($inputs["translates"][$lyric->parent]);
				}
			}
			foreach ($inputs["translates"] as $lyric => $translate) {
				$lyr = new lyric();
				$lyr->song = $song->id;
				$lyr->lang = $inputs["lang"];
				$lyr->text = $translate;
				$lyr->parent = $lyric;
				$lyr->status = lyric::draft;
				$lyr->save();

				$clyr = new ContributeLyric();
				$clyr->contribute = $contribute->id;
				$clyr->parent = $lyric;
				$clyr->lyric = $lyr->id;
				$clyr->text = $lyr->text;
				$clyr->save();
			}
			if (isset($inputs["title"]) and $song->title($inputs["lang"]) != $inputs["title"]) {
				$contribute->setParam("title", $inputs["title"]);
			}
		} else {
			$this->response->setData(array(
				"contribute" => false,
			));
		}
		$this->response->setStatus(true);
		return $this->response;
	}
	public function sync($data) {
		$song = new songObj();
		$song->where("id", $data["song"]);
		$song->where("synced", songObj::synced, "!=");
		if (!$song = $song->getOne()) {
			throw new NotFound();
		}
		$view = view::byName(views\contributes\songs\Sync::class);
		$this->response->setView($view);
		$view->setSong($song);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function doSync($data) {
		$song = new songObj();
		$song->where("id", $data["song"]);
		$song->where("synced", songObj::synced, "!=");
		if (!$song = $song->getOne()) {
			throw new NotFound();
		}
		$view = view::byName(views\contributes\songs\Sync::class);
		$this->response->setView($view);
		$view->setSong($song);
		$inputsRules = array(
			"time" => array(),
		);
		$inputs = $this->checkinputs($inputsRules);
		$lyric = new lyric();
		$lyric->where("song", $song->id);
		$lyric->where("lang", $song->lang);
		$lyrics = $lyric->get();
		foreach ($lyrics as $lyr) {
			if (isset($inputs["time"][$lyr->id])) {
				if ($inputs["time"][$lyr->id]) {
					list($min, $sec) = explode(":", $inputs["time"][$lyr->id], 2);
					if (!$min or !$sec) {
						throw new inputValidation("time[{$lyr->id}]");
					}
					if ($min == "00" and $sec == "00") {
						unset($inputs["time"][$lyr->id]);
						continue;
					}
					$inputs["time"][$lyr->id] = ($min * 60) + $sec;
				} else {
					unset($inputs["time"][$lyr->id]);
				}
			}
		}
		if ($inputs["time"]) {
			$contribute = new Contribute();
			$contribute->title = translator::trans("ghafiye.contributes.title.songs.synce", array("title" => $song->title($song->lang)));
			$contribute->user = authentication::getID();
			$contribute->song = $song->id;
			$contribute->lang = $song->lang;
			$contribute->type = songs\Sync::class;
			$contribute->point = (new songs\Sync)->getPoint();
			$contribute->save();
			foreach ($lyrics as $lyr) {
				if (isset($inputs["time"][$lyr->id])) {
					$clyr = new ContributeLyric();
					$clyr->contribute = $contribute->id;
					$clyr->lyric = $lyr->id;
					$clyr->text = $lyr->text;
					$clyr->time = $inputs["time"][$lyr->id];
					$clyr->save();
				}
			}
		} else {
			$this->response->setData(array(
				"contribute" => false
			));
		}
		$this->response->setStatus(true);
		return $this->response;
	}
	public function edit($data) {
		$song = new songObj();
		$song->where("id", $data["song"]);
		$song->where("status", songObj::publish);
		if (!$song = $song->getOne()) {
			throw new NotFound();
		}
		$view = view::byName(views\contributes\songs\Edit::class);
		$this->response->setView($view);
		$view->setSong($song);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function update($data) {
		$song = new songObj();
		$song->where("id", $data["song"]);
		$song->where("status", songObj::publish);
		if (!$song = $song->getOne()) {
			throw new NotFound();
		}
		$view = view::byName(views\contributes\songs\Edit::class);
		$this->response->setView($view);
		$view->setSong($song);
		$inputsRules = array(
			"image" => array(
				"type" => "file",
				"optional" => true,
				"empty" => true,
			),
			"title" => array(
				"type" => "string",
			),
			"lyrics" => array(),
		);
		$inputs = $this->checkinputs($inputsRules);
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
		$lyric = new lyric();
		$lyric->where("song", $song->id);
		$lyric->where("lang", $song->lang);
		$lyric->where("status", lyric::published);
		$lyric->orderBy("ordering", "ASC");
		$lyrics = array();
		foreach ($lyric->get() as $lyric) {
			$lyrics[$lyric->id] = $lyric;
		}
		$ids = array();
		foreach ($inputs["lyrics"] as $key => $lyric) {
			if (isset($lyric["text"]) and !$lyric["text"]) {
				unset($inputs["lyrics"][$key]);
				continue;
			}
			if (isset($lyric["id"])) {
				if ($lyric["id"]) {
					$ids[] = $lyric["id"];
					if (!isset($lyrics[$lyric["id"]])) {
						throw new inputValidation("lyrics[{$key}][text]");
					}
				} else {
					unset($inputs["lyrics"][$key]["id"]);
				}
			}
		}
		$inputs["lyrics"] = array_values($inputs["lyrics"]);
		foreach ($inputs["lyrics"] as $key => $lyric) {
			if (isset($lyric["id"])) {
				$lyr = $lyrics[$lyric["id"]];
				if ($lyr->text == $lyric["text"] and $lyr->ordering == $key + 1) {
					unset($inputs["lyrics"][$key]);
				}
			}
		}
		if (
			$inputs["lyrics"] or 
			count($ids) != count($lyrics) or 
			$song->title($song->lang) != $inputs["title"] or
			isset($inputs["image"])
		) {
			$contribute = new Contribute();
			$contribute->title = translator::trans("ghafiye.contributes.title.songs.edit", array("title" => $song->title($song->lang)));
			$contribute->user = authentication::getID();
			$contribute->song = $song->id;
			$contribute->lang = $song->lang;
			$contribute->type = songs\Edit::class;
			$contribute->point = (new songs\Edit)->getPoint();
			$contribute->save();
			
			$deleted = array();
			foreach ($lyrics as $lyric) {
				if (!in_array($lyric->id, $ids)) {
					$clyr = new ContributeLyric();
					$clyr->contribute = $contribute->id;
					$clyr->text = $lyric->text;
					$clyr->lyric = $lyric->id;
					$clyr->ordering = $lyric->ordering;
					$clyr->save();
					$deleted[] = $clyr->id;
				}
			}
			$orderingUpLyrics = array();
			$orderingDownLyrics = array();
			foreach ($inputs["lyrics"] as $key => $lyr) {
				$ordering = $key + 1;
				$clyr = new ContributeLyric();
				$clyr->contribute = $contribute->id;
				$clyr->text = $lyr["text"];
				if (isset($lyr["id"])) {
					$lyric = $lyrics[$lyr["id"]];
					$clyr->lyric = $lyric->id;
					if ($lyric->text != $lyr["text"]) {
						$clyr->old_text = $lyric->text;
					}
				} else {
					$lyric = new lyric();
					$lyric->song = $song->id;
					$lyric->lang = $song->lang;
					$lyric->time = 0;
					$lyric->text = $lyr["text"];
					$lyric->status = lyric::draft;
					$lyric->ordering = $ordering;
					$lyric->save();

					$clyr->lyric = $lyric->id;
				}
				$clyr->ordering = $ordering;
				$clyr->save();
				if (isset($lyr["id"])) {
					if ($lyric->ordering > $ordering) {
						$orderingUpLyrics[] = $clyr->id;
					} else if ($lyric->ordering < $ordering) {
						$orderingDownLyrics[] = $clyr->id;
					}
				}
			}
			if ($orderingUpLyrics) {
				$contribute->setParam("orderingUpLyrics", $orderingUpLyrics);
			}
			if ($orderingDownLyrics) {
				$contribute->setParam("orderingDownLyrics", $orderingDownLyrics);
			}
			if ($deleted) {
				$contribute->setParam("deletedLyrics", $deleted);
			}
			if ($song->title($song->lang) != $inputs["title"]) {
				$contribute->setParam("title", $inputs["title"]);
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
				$path = "storage/public/songs/" . $tmpfile->md5() . $type_name;
				$contribute->setParam("image", $path);
				$image = new file\local(packages::package("ghafiye")->getFilePath($path));
				$image->getDirectory()->make(true);
				$tmpfile->copyTo($image);
			}
			$song->save();
		} else {
			$this->response->setData(array(
				"contribute" => false,
			));
		}
		$this->response->setStatus(true);
		return $this->response;
	}
}
