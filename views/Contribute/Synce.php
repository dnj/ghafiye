<?php
namespace packages\ghafiye\views\contribute;
use packages\base;
use packages\ghafiye\{views\listview, authentication, Contribute};

class Synce extends listview {
	public function export(): array {
		$export = parent::export();
		$items = array();
		foreach ($this->getSynceTracks() as $track) {
			$item = array(
				"id" => $track->id,
				"image" => $track->getImage(32, 32),
				"song" => array(
					"title" => $track->title(),
					"url" => $track->url(),
				),
				"singer" => array(
					"name" => $track->getSinger()->name(),
					"url" => base\url($track->getSinger()->encodedName()),
				),
			);
			$items[] = $item;
		}
		$export["data"]["items"] = $items;
		return $export;
	}
	protected function getUser() {
		return authentication::getUser();
	}
	protected function getWeeklyUsersLeaderboard() {
		return Contribute::getWeeklyUsersLeaderboard();
	}
	protected function getSynceTracks() {
		return $this->getDataList();
	}
}
