<?php
namespace packages\ghafiye\views\profile;
use packages\base;
use packages\base\date;
use packages\ghafiye\{views\listview, User, song\person};

class Contributes extends listview {
	public function setUser(User $user) {
		$this->setData($user, "user");
	}
	public function getUser() {
		return $this->getData("user");
	}
	public function setContributes(array $contributes) {
		$this->setData($contributes, "contributes");
	}
	public function getContributes() {
		return $this->getData("contributes");
	}
	public function setFavoritSongs(array $songs) {
		$this->setData($songs, "songs");
	}
	public function getFavoritSongs() {
		return $this->getData("songs");
	}
	public function export(): array {
		$export = parent::export();
		$user = $this->getUser();
		$username = $user->getFullName();
		$userAvatar = $user->getAvatar(32, 32);
		$items = array();
		foreach ($this->getContributes() as $contribute) {
			$singer = $contribute->song->getPerson(person::singer);
			$item = array(
				"title" => $contribute->title,
				"done_at" => date::relativeTime($contribute->done_at),
				"user" => array(
					"name" => $username,
					"avatar" => $userAvatar,
				),
				"song" => array(
					"title" => $contribute->song->title(),
					"avatar" => $contribute->song->getImage(48, 48),
					"url" => base\url($singer->encodedName() . '/' . $contribute->song->encodedTitle()),
					"singer" => array(
						"title" => $singer->name(),
						"url" => base\url($singer->encodedName()),
					),
				),
			);
			$items[] = $item;
		}
		$export["data"]["items"] = $items;
		return $export;
	}
}
