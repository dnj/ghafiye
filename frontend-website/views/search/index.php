<?php
namespace themes\musixmatch\views\search;
use \packages\base;
use \packages\base\options;
use \packages\base\packages;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \packages\ghafiye\song;
use \packages\ghafiye\person;
use \packages\ghafiye\song\person as songPerson;
use \packages\ghafiye\views\search\index as homepage;

use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\listTrait;
class index extends homepage{
	use viewTrait, listTrait;
	function __beforeLoad(){
		$this->addBodyClass('search article');
	}
	protected function getAvatar(person $person){
		return(packages::package("ghafiye")->url($person->avatar ? $person->avatar : options::get("packages.ghafiye.persons.deafault_image")));
	}
	protected function getImage(song $song){
		return(packages::package("ghafiye")->url($song->image ? $song->image : options::get("packages.ghafiye.songs.deafault_image")));
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
			$songsData[] = array(
				'link' => "{$song->getPerson(songPerson::singer)->encodedName($song->showing_lang)}/{$song->encodedTitle($song->showing_lang)}",
				'title' => $song->title($song->showing_lang),
				'image' => $this->getImage($song),
				'signer' => $song->getPerson(songPerson::singer)->name($song->showing_lang)
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
				'image' => $this->getAvatar($person)
			);
		}
		return $personsData;
	}
}
