<?php
namespace packages\ghafiye\views\panel\genre;
use \packages\ghafiye\genre;
use \packages\ghafiye\views\form;
class delete extends form{
	public function setGenre(genre $genre){
		$this->setData($genre, "genre");
	}
	protected function getGenre(){
		return $this->getData("genre");
	}
}
