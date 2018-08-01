<?php
namespace packages\ghafiye\views\profile;
use packages\base;
use packages\base\date;
use packages\ghafiye\{views\listview, User, song\person};

class Favorites extends listview {
	public function setUser(User $user) {
		$this->setData($user, "user");
	}
	public function getUser() {
		return $this->getData("user");
	}
	public function setFavoriteSongs(array $songs) {
		$this->setData($songs, "songs");
	}
	public function getFavoriteSongs() {
		return $this->getData("songs");
	}
}
