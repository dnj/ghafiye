<?php
namespace packages\ghafiye\views\panel\genre;
use \packages\ghafiye\genre;
use \packages\ghafiye\views\form;
use \packages\ghafiye\authorization;
class edit extends form{
	public function setGenre(genre $genre){
		$this->setData($genre, "genre");
		$this->setDataForm($genre->musixmatch_id, "musixmatch_id");
	}
	protected function getGenre(){
		return $this->getData("genre");
	}
}
