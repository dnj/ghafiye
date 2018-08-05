<?php
namespace packages\ghafiye\controllers;
use packages\base\{db, response};
use packages\userpanel\controller;
use packages\ghafiye\{view, views, authorization, song, Contribute as contributeObj};

class Contribute extends controller {
	public $authentication = true;
	public function main(): response {
		$view = view::byName(views\contribute\Main::class);
		$this->response->setView($view);
		db::join("ghafiye_songs_translates_progress", "ghafiye_songs_translates_progress.song=ghafiye_songs.id", "INNER");
		$song = new song();
		$song->where("ghafiye_songs_translates_progress.progress", 100, "!=");
		$song->where("ghafiye_songs_translates_progress.lang", "fa");
		$song->where("ghafiye_songs.status", song::publish);
		$song->where("ghafiye_songs.lang", "fa", "!=");
		$song->orderBy("ghafiye_songs.release_at", "DESC");
		$view->setTranslateTracks($song->get(7, "ghafiye_songs.*"));
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
		$song = new song();
		db::join("ghafiye_songs_translates_progress", "ghafiye_songs_translates_progress.song=ghafiye_songs.id", "INNER");
		$song = new song();
		$song->where("ghafiye_songs_translates_progress.progress", 100, "!=");
		$song->where("ghafiye_songs_translates_progress.lang", "fa");
		$song->where("ghafiye_songs.status", song::publish);
		$song->where("ghafiye_songs.lang", "fa", "!=");
		$song->orderBy("ghafiye_songs.release_at", "DESC");
		$song->pageLimit = $this->items_per_page;
		$songs = $song->paginate($this->page, "ghafiye_songs.*");
		$view->setDataList($songs);
		$view->setPaginate($this->page, $song->totalCount, $this->items_per_page);
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
	public function view($data): response {
		$contribute = new contributeObj();
		$contribute->where("id", $data["contribute"]);
		$contribute->where("status", contributeObj::accepted);
		if (!$contribute = $contribute->getOne()) {
			throw new NotFound();
		}
		$view = view::byName(views\contribute\View::class);
		$this->response->setView($view);
		$view->setContribute($contribute);
		$this->response->setStatus(true);
		return $this->response;
	}
}