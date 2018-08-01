<?php
namespace packages\ghafiye\controllers\panel\songs\lyrics;
use packages\base\{db, NotFound, view\error, inputValidation, views\FormError, response};
use packages\userpanel;
use packages\userpanel\{controller, log};
use packages\ghafiye\{view, views, authorization, authentication, song\lyric\Description};

class Descriptions extends controller {
	protected $authentication = true;
	public function search(): response {
		authorization::haveOrFail("songs_lyrics_descriptions_search");
		$view = view::byName(views\panel\songs\lyrics\descriptions\Search::class);
		$this->response->setView($view);
		$types = authorization::childrenTypes();
		$description = new Description();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_songs_lyrices_description.user", "inner");
		if ($types) {
			$description->where("userpanel_users.type", $types, "in");
		} else {
			$description->where("userpanel_users.id", authentication::getID());
		}
		$inputsRules = array(
			"id" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"user" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"song" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"status" => array(
				"values" => array(Description::accepted, Description::waitForAccept, Description::rejected),
				"optional" => true,
				"empty" => true,
			),
			"word" => array(
				"type" => "string",
				"optional" => true,
				"empty" => true,
			),
			"comparison" => array(
				"values" => array("equals", "startswith", "contains"),
				"default" => "contains",
				"optional" => true,
			),
		);
		$inputs = $this->checkinputs($inputsRules);
		foreach (array("id", "user", "status") as $item) {
			if (isset($inputs[$item]) and $inputs[$item]) {
				$comparison = $inputs["comparison"];
				if (in_array($item, array("id", "user", "status"))) {
					$comparison = "equals";
				}
				$description->where("ghafiye_songs_lyrices_description.{$item}", $inputs[$item], $comparison);
			}
		}
		if (isset($inputs["song"])) {
			if ($inputs["song"]) {
				db::join("ghafiye_songs_lyrices", "ghafiye_songs_lyrices.id=ghafiye_songs_lyrices_description.lyric", "INNER");
				db::join("ghafiye_songs", "ghafiye_songs.id=ghafiye_songs_lyrices.song", "LEFT");
				$description->where("ghafiye_songs.id", $inputs["song"]);
			}
		}
		if (isset($inputs["word"]) and $inputs["word"]) {
			$description->where("ghafiye_songs_lyrices_description.text", $inputs["word"], $inputs["comparison"]);
		}
		$description->pageLimit = $this->items_per_page;
		$description->orderBy("ghafiye_songs_lyrices_description.sent_at", "DESC");
		$description->groupBy("ghafiye_songs_lyrices_description.id");
		$descriptions = $description->paginate($this->page, "ghafiye_songs_lyrices_description.*");
		$this->total_pages = $description->totalPages;
		$view->setDataList($descriptions);
		$view->setPaginate($this->page, db::totalCount(), $this->items_per_page);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function edit($data) {
		authorization::haveOrFail("songs_lyrics_descriptions_edit");
		$view = view::byName(views\panel\songs\lyrics\descriptions\Edit::class);
		$this->response->setView($view);
		$types = authorization::childrenTypes();
		$description = new Description();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_songs_lyrices_description.user", "inner");
		if ($types) {
			$description->where("userpanel_users.type", $types, "in");
		} else {
			$description->where("userpanel_users.id", authentication::getID());
		}
		$description->where("ghafiye_songs_lyrices_description.id", $data["description"]);
		if (!$description = $description->getOne("ghafiye_songs_lyrices_description.*")) {
			throw new NotFound();
		}
		$view->setLyricDesctription($description);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function update($data) {
		authorization::haveOrFail("songs_lyrics_descriptions_edit");
		$view = view::byName(views\panel\songs\lyrics\descriptions\Edit::class);
		$this->response->setView($view);
		$types = authorization::childrenTypes();
		$description = new Description();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_songs_lyrices_description.user", "inner");
		if ($types) {
			$description->where("userpanel_users.type", $types, "in");
		} else {
			$description->where("userpanel_users.id", authentication::getID());
		}
		$description->where("ghafiye_songs_lyrices_description.id", $data["description"]);
		if (!$description = $description->getOne("ghafiye_songs_lyrices_description.*")) {
			throw new NotFound();
		}
		$view->setLyricDesctription($description);
		$inputsRules = array(
			"text" => array(
				"type" => "string",
				"optional" => true,
			),
			"status" => array(
				"values" => array(Description::accepted, Description::waitForAccept, Description::rejected),
				"optional" => true,
			),
		);
		$inputs = $this->checkinputs($inputsRules);
		if (isset($inputs["text"])) {
			if ($inputs["text"]) {
				$description->text = $inputs["text"];
			}
		}
		if (isset($inputs["status"])) {
			if ($inputs["status"]) {
				$description->status = $inputs["status"];
			}
		}
		$description->save();
		$this->response->setStatus(true);
		return $this->response;
	}
	public function delete($data) {
		authorization::haveOrFail("songs_lyrics_descriptions_delete");
		$view = view::byName(views\panel\songs\lyrics\descriptions\Delete::class);
		$this->response->setView($view);
		$types = authorization::childrenTypes();
		$description = new Description();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_songs_lyrices_description.user", "inner");
		if ($types) {
			$description->where("userpanel_users.type", $types, "in");
		} else {
			$description->where("userpanel_users.id", authentication::getID());
		}
		$description->where("ghafiye_songs_lyrices_description.id", $data["description"]);
		if (!$description = $description->getOne("ghafiye_songs_lyrices_description.*")) {
			throw new NotFound();
		}
		$view->setLyricDesctription($description);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function terminate($data) {
		authorization::haveOrFail("songs_lyrics_descriptions_delete");
		$view = view::byName(views\panel\songs\lyrics\descriptions\Delete::class);
		$this->response->setView($view);
		$types = authorization::childrenTypes();
		$description = new Description();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_songs_lyrices_description.user", "inner");
		if ($types) {
			$description->where("userpanel_users.type", $types, "in");
		} else {
			$description->where("userpanel_users.id", authentication::getID());
		}
		$description->where("ghafiye_songs_lyrices_description.id", $data["description"]);
		if (!$description = $description->getOne("ghafiye_songs_lyrices_description.*")) {
			throw new NotFound();
		}
		$description->delete();
		$this->response->Go(userpanel\url("songs/lyrics/descriptions"));
		$this->response->setStatus(true);
		return $this->response;
	}
}