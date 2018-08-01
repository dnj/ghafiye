<?php
namespace packages\ghafiye\controllers;
use packages\base\{db, response};
use packages\ghafiye\{view, views, controller, authorization, song};

class Contribute extends controller {
	public $authentication = true;
	public function main(): response {
		$view = view::byName(views\contribute\Main::class);
		$this->response->setView($view);
		$data = db::rawQuery("SELECT * FROM `ghafiye_songs` WHERE `id` IN( SELECT `song` FROM `ghafiye_songs_lyrices` GROUP BY `song` HAVING COUNT(DISTINCT `lang`) = 1 ORDER BY `song` DESC ) AND `lang` != 'fa' AND `status`  = " . song::publish . " ORDER BY release_at DESC LIMIT 7");
		$tracks = [];
		foreach ($data as $item) {
			$tracks[] = new song($item);
		}
		$view->setTranslateTracks($tracks);
		$song = new song();
		$song->where("synced", song::synced, "!=");
		$song->where("status", song::publish);
		$song->orderBy("release_at", "DESC");
		$tracks = $song->get(7);
		$view->setSyncTracks($tracks);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function translate(): response {
		$view = view::byName(views\contribute\Translate::class);
		$this->response->setView($view);
		$data = db::rawQuery("SELECT SQL_CALC_FOUND_ROWS * FROM `ghafiye_songs` WHERE `id` IN( SELECT `song` FROM `ghafiye_songs_lyrices` GROUP BY `song` HAVING COUNT(DISTINCT `lang`) = 1 ORDER BY `song` DESC ) AND `lang` != 'fa' AND `status`  = " . song::publish . " ORDER BY `release_at` DESC LIMIT ?, ?",
		array(($this->page - 1) * $this->items_per_page, $this->items_per_page));
		$totalCount = db::rawQueryValue("SELECT FOUND_ROWS()")[0];
		$songs = [];
		foreach ($data as $item) {
			$songs[] = new song($item);
		}
		$view->setDataList($songs);
		$view->setPaginate($this->page, $totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function synce(): response {
		$view = view::byName(views\contribute\Synce::class);
		$this->response->setView($view);
		$song = new song();
		$song->where("synced", song::synced, "!=");
		$song->where("status", song::publish);
		$song->orderBy("release_at", "DESC");
		$song->pageLimit = $this->items_per_page;
		$songs = $song->paginate($this->page, "ghafiye_songs.*");
		$view->setDataList($songs);
		$view->setPaginate($this->page, db::totalCount(), $this->items_per_page);
		$this->response->setStatus(true);
		return $this->response;
	}
}