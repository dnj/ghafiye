<?php
namespace packages\ghafiye\views\contributes\songs;
use packages\ghafiye\{views\form, authentication, genre, group, person, album};

class Add extends form {
	public function setPerson(person $person) {
		$this->setData($person, "person");
		$this->setDataForm($person->id, "person");
		$this->setDataForm($person->name(), "person_name");
	}
	public function setGroup(group $group) {
		$this->setData($group, "group");
		$this->setDataForm($group->id, "group");
		$this->setDataForm($group->title(), "group_name");
	}
	public function setAlbum(album $album) {
		$this->setData($album, "album");
		$this->setDataForm($album->id, "album");
		$this->setDataForm($album->title(), "album_name");
	}
	protected function getPerson() {
		return $this->getData("person");
	}
	protected function getGroup() {
		return $this->getData("group");
	}
	protected function getAlbum() {
		return $this->getData("album");
	}
	protected function getGenres(): array {
		return genre::get();
	}
}
