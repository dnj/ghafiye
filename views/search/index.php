<?php
namespace packages\ghafiye\views\search;
use \packages\ghafiye\views\listview as list_view;
use \packages\base\views\traits\form as formTrait;
use \packages\ghafiye\song\person as songPerson;
use \packages\ghafiye\person;
class index extends list_view{
	use formTrait;
	public function setSongs($songs){
		$this->setData($songs, "songs");
	}
	protected function getSongs(){
		return $this->getData("songs");
	}
	public function setPersons($persons){
		$this->setData($persons, "persons");
	}
	protected function getPersons(){
		return $this->getData("persons");
	}
	public function setWord($word){
		$this->setData($word, "word_search");
	}
	public function getWord(){
		return $this->getData("word_search");
	}
	public function setType($type){
		$this->setData($type, "type_search");
	}
	public function getType(){
		return $this->getData("type_search");
	}
	public function setResults($results){
		$this->setData($results, "results");
	}
	protected function getResults(){
		return $this->getData("results");
	}
	public function export(){
		$export = parent::export();
		$export["data"]["items"] = array();
		if($this->getPersons()){
			foreach($this->getPersons() as $person){
				$item = $person->toArray();
				$item['type'] = 'person';
				$item["name"] = $person->name($person->lang);
				$item["encodedName"] = $person->encodedName($person->lang);
				$item["avatar"] = $person->getAvatar(32, 32);
				$export['data']['items'][] = $item;
			}
		}
		if($this->getSongs()){
			foreach($this->getSongs() as $song){
				$person = $song->getPerson(songPerson::singer);
				$item = $song->toArray();
				$item['type'] = 'song';
				$item['title'] = $song->title($song->lang);
				$item['encodedTitle'] = $song->encodedTitle($song->lang);
				$item['singer'] = $person->toArray();
				$item['singer']['name'] = $person->name($song->lang);
				$item['singer']['encodedName'] = $person->encodedName($song->lang);
				$item['singer']['avatar'] = $person->getAvatar(32, 32);
				$item["image"] = $song->getImage(32, 32);
				$export['data']['items'][] = $item;
			}
		}
		$export['data']['items'] = array_slice($export['data']['items'], 0, 10);
		return $export;
	}
}
