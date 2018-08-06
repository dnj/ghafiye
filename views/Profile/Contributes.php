<?php
namespace packages\ghafiye\views\profile;
use packages\base;
use packages\base\date;
use packages\ghafiye\{views\listview, User, song, song\person, contributes\songs, contributes\persons, contributes\groups, contributes\albums};

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
			$item = array(
				"id" => $contribute->id,
				"title" => $contribute->title,
				"done_at" => date::relativeTime($contribute->done_at),
				"user" => array(
					"id" => $user->id,
					"name" => $username,
					"avatar" => $userAvatar,
				),
			);
			switch ($contribute->type) {
				case (songs\Add::class):
				case (songs\Translate::class):
				case (songs\Sync::class):
					$item["image"] = $contribute->song->getImage(48, 48);
					$item["song"] = array(
						"title" => $contribute->song->title(),
						"url" => base\url($contribute->song->getSinger()->encodedName() . '/' . $contribute->song->encodedTitle()),
						"singer" => array(
							"title" => $contribute->song->getSinger()->name(),
							"url" => base\url($contribute->song->getSinger()->encodedName()),
						),
					);
					break;
				case (persons\Add::class):
				$item["image"] = $contribute->person->getAvatar(48, 48);
					$item["person"] = array(
						"name" => $contribute->person->name($contribute->lang),
						"url" => base\url($contribute->person->encodedName()),
					);
					break;
				case (groups\Add::class):
					$item["image"] = $contribute->group->getAvatar(48, 48);
					$item["group"] = array(
						"name" => $contribute->group->name($contribute->lang),
						"url" => base\url($contribute->group->encodedName()),
					);
					break;
				case (albums\Add::class):
					$item["image"] = $contribute->album->getImage(48, 48);
					$song = new song();
					$song->where("album", $contribute->album->id);
					$song->where("status", song::publish);
					if ($song = $song->getOne()) {
						$item["group"] = array(
							"name" => $contribute->album->title($contribute->lang),
							"url" => base\url($song->getSinger()->encodedName($contribute->lang) . '/albums/' . $contribute->album->encodedTitle($contribute->lang)),
						);
					} else {
						$item["group"] = array(
							"name" => $contribute->album->title($contribute->lang),
							"url" => "#",
						);
					}
					break;
			}
			$items[] = $item;
		}
		$export["data"]["items"] = $items;
		return $export;
	}
}
