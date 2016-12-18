<?php
namespace packages\ghafiye\views\explore;
use \packages\base\db\dbObject;
use \packages\ghafiye\view;
use \packages\ghafiye\genre as genreObj;
class genre extends view{
	public function setSongs($songs){
		$this->setData($songs, 'songs');
	}
	public function getSongs(){
		return $this->getData('songs');
	}
	public function setGenre(genreObj $genre){
		$this->setData($genre, 'genre');
	}
	public function getGenre(){
		return $this->getData('genre');
	}
	public function setSongLanguage($lang){
		$this->setData($lang, 'songsLang');
	}
	public function getSongLanguage(){
		return $this->getData('songsLang');
	}
	public function export(){
		return array(
			'data' => array(
				'genre' => $this->getGenre()->toArray(),
				'songs' => dbObject::objectToArray($this->getSongs()),
				'lang' => $this->getSongLanguage()
			)
		);
	}
}
