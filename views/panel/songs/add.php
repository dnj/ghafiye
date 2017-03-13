<?php
namespace packages\ghafiye\views\panel\song;
use \packages\ghafiye\views\form;
class add extends form{
	public function setAllowLangs($allowlangs){
		$this->setData($allowlangs, 'allowlangs');
	}
	protected function getAllowLangs(){
		return $this->getData("allowlangs");
	}
	public function setGenres($genres){
		$this->setData($genres, 'genres');
	}
	protected function getGenres(){
		return $this->getData("genres");
	}
}
