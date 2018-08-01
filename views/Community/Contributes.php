<?php
namespace packages\ghafiye\views\community;
use packages\ghafiye\views\listview;

class Contributes extends listview {
	public function setContrbutes(array $contributes) {
		$this->setData($contributes, "contributes");
	}
	public function getContributes() {
		return $this->getData("contributes");
	}
	public function setWeeklyUsersLeaderboard(array $users) {
		$this->setData($users, "WeeklyUsersLeaderboard");
	}
	public function getWeeklyUsersLeaderboard() {
		return $this->getData("WeeklyUsersLeaderboard");
	}
}
