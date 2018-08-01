<?php
namespace packages\ghafiye\views\contribute;
use packages\ghafiye\{view, authentication, Contribute};

class Main extends view {
	public function setTranslateTracks(array $tracks) {
		$this->setData($tracks, "traslatetracks");
	}
	public function setSyncTracks(array $tracks) {
		$this->setData($tracks, "synctracks");
	}
	protected function getUser() {
		return authentication::getUser();
	}
	protected function getWeeklyUsersLeaderboard() {
		return Contribute::getWeeklyUsersLeaderboard();
	}
	protected function getTranslateTracks() {
		return $this->getData("traslatetracks");
	}
	protected function getSynceTracks() {
		return $this->getData("synctracks");
	}
}
