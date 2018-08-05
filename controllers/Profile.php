<?php
namespace packages\ghafiye\controllers;
use packages\base;
use packages\userpanel;
use packages\base\{response, NotFound, db};
use packages\ghafiye\{view, views, controller, User, Contribute, song, authentication};

class Profile extends controller {
	public function contributes($data): response {
		if (!isset($data["user"])) {
			$data = array();
			if (authentication::check()) {
				$data["user"] = authentication::getID();
			} else {
				$this->response->Go(userpanel\url("login", array("backTo" => base\url("profile"))));
				$this->response->setStatus(true);
				return $this->response;
			}
		}
		if (!$user = User::byId($data["user"])) {
			throw new NotFound();
		}
		$view = view::byName(views\profile\Contributes::class);
		$view->setUser($user);
		$contribute = new Contribute();
		$contribute->where("user", $user->id);
		$contribute->orderBy("done_at", "DESC");
		$contribute->where("status", Contribute::accepted);
		$contribute->pageLimit = $this->items_per_page;
		$contributes = $contribute->paginate($this->page);
		$view->setContributes($contributes);
		$view->setPaginate($this->page, db::totalCount(), $contribute->pageLimit);
		db::join("ghafiye_songs_likes", "ghafiye_songs_likes.song=ghafiye_songs.id", "INNER");
		$song = new song;
		$song->where("ghafiye_songs_likes.user", $user->id);
		$view->setFavoritSongs($song->get(10, "ghafiye_songs.*"));
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function favorites($data) {
		if (!isset($data["user"])) {
			if (authentication::check()) {
				$data["user"] = authentication::getID();
			} else {
				$this->response->Go(userpanel\url("login", array("backTo" => base\url("profile"))));
				$this->response->setStatus(true);
				return $this->response;
			}
		}
		if (!$user = User::byId($data["user"])) {
			throw new NotFound();
		}
		$view = view::byName(views\profile\Favorites::class);
		$view->setUser($user);
		db::join("ghafiye_songs_likes", "ghafiye_songs_likes.song=ghafiye_songs.id", "INNER");
		$song = new song;
		$song->where("ghafiye_songs_likes.user", $user->id);
		$song->pageLimit = $this->items_per_page;
		$songs = $song->paginate($this->page, "ghafiye_songs.*");
		$view->setFavoriteSongs($songs);
		$view->setPaginate($this->page, db::totalCount(), $song->pageLimit);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
}
