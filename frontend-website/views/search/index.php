<?php
namespace themes\musixmatch\views\search;
use \packages\base\translator;

use \packages\ghafiye\song;
use \packages\ghafiye\person;
use \packages\ghafiye\song\person as songPerson;
use \packages\ghafiye\views\search\index as homepage;

use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\listTrait;
class index extends homepage{
	use viewTrait, listTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans("result.searchBy", array("word"=>$this->getWord())));
		$this->addBodyClass('search article');
	}
	public function results(){
		if($this->getType()){
			switch($this->getType()){
				case("songs"):
					return $this->createSongsData($this->getSongs());
				case("persons"):
					return $this->createPersonsData($this->getPersons());
				case("lyrics"):
					return $this->createSongsData($this->getSongs());
			}
		}else{
			return $this->createSongsData($this->getSongs());
		}
	}
	private function createSongsData($songs){
		$songsData = array();
		foreach($songs as $song){
			$person = $song->getPerson(songPerson::singer);
			$songsData[] = array(
				'link' => "{$person->encodedName($song->showing_lang)}/{$song->encodedTitle($song->showing_lang)}",
				'title' => $song->title($song->showing_lang),
				'image' => $song->getImage(32, 32),
				'signer' => $person->name($song->showing_lang)
			);
		}
		return $songsData;
	}
	private function createPersonsData($persons){
		$personsData = array();
		foreach($persons as $person){
			$personsData[] = array(
				'link' => $person->name($person->showing_lang),
				'title' => $person->name($person->showing_lang),
				'image' => $person->getAvatar(32, 32)
			);
		}
		return $personsData;
	}
}
