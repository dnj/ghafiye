<?php
namespace packages\ghafiye\controllers;
use \packages\base;
use \packages\base\db;
use \packages\base\http;
use \packages\base\NotFound;
use \packages\base\inputValidation;
use \packages\base\db\parenthesis;
use \packages\base\views\FormError;

use \packages\ghafiye\{view, controller, song, person, group};
use \packages\ghafiye\person\name as personName;

class search extends controller{
	public function byLyrics($data){
		$data['type'] = 'lyrics';
		return $this->index($data);
	}
	public function byPersons($data){
		$data['type'] = 'persons';
		return $this->index($data);
	}
	public function bySongs($data){
		$data['type'] = 'songs';
		return $this->index($data);
	}
	public function byGroup($data){
		$data['type'] = 'group';
		return $this->index($data);
	}
	public function index($data){
		$view = view::byName("\\packages\\ghafiye\\views\\search\\index");
		$inputsRules = array(
			'word' => array(
				'type' => 'string',
				'optional' => true
			)
		);
		$this->response->setStatus(true);

		try{
			$inputs = $this->checkinputs($inputsRules);
			if(!isset($data['word']))
				throw new notfound;

			$song = new song();
			$person = new person();

			db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "INNER");
			db::join("ghafiye_persons", "ghafiye_songs_persons.person=ghafiye_persons.id", "INNER");
			db::join("ghafiye_persons_names", "ghafiye_persons_names.person=ghafiye_persons.id", "INNER");
			$song->where("ghafiye_persons_names.name", $data['word'], "contains");
			db::setQueryOption("DISTINCT");
			$songsByArtistName = $song::where("status", song::publish)->get(null, array("ghafiye_songs.*", "ghafiye_persons_names.lang as `showing_lang`"));
		

			db::join("ghafiye_groups_titles", "ghafiye_groups_titles.group_id=ghafiye_songs.group", "LEFT");
			$song->where("ghafiye_groups_titles.title", $data['word'], "contains");
			db::setQueryOption("DISTINCT");
			$songsByGroupName = $song::where("status", song::publish)->get(null, array("ghafiye_songs.*", "ghafiye_groups_titles.lang as `showing_lang`"));

			db::join("ghafiye_songs_titles", "ghafiye_songs_titles.song=ghafiye_songs.id", "INNER");
			$song->where("ghafiye_songs_titles.title", $data['word'], "contains");
			db::setQueryOption("DISTINCT");
			$songsByTitle = $song::where("status", song::publish)->get(null, array("ghafiye_songs.*", "ghafiye_songs_titles.lang as `showing_lang`"));


			db::join("ghafiye_songs_lyrices", "ghafiye_songs_lyrices.song=ghafiye_songs.id", "INNER");
			$song->where("ghafiye_songs_lyrices.text", $data['word'], "contains");
			db::setQueryOption("DISTINCT");
			$songsByLyrice = $song::where("status", song::publish)->get(null, array("ghafiye_songs.*", "ghafiye_songs_lyrices.lang as `showing_lang`"));
			$songs = array_merge($songsByArtistName, $songsByTitle, $songsByLyrice, $songsByGroupName);
			$c = count($songs);
			for($key1=0;$key1<$c-1;$key1++){
				if(!isset($songs[$key1])){
					continue;
				}
				for($key2=$key1+1;$key2<$c;$key2++){
					if(!isset($songs[$key2])){
						continue;
					}
					if($songs[$key1]->id == $songs[$key2]->id){
						unset($songs[$key2]);
					}
				}

			}
			db::join("ghafiye_songs_persons", "ghafiye_songs_persons.person=ghafiye_persons.id", "INNER");
			db::join("ghafiye_songs", "ghafiye_songs_persons.song=ghafiye_songs.id", "INNER");
			db::join("ghafiye_persons_names", "ghafiye_persons_names.person=ghafiye_persons.id", "INNER");
			db::join("ghafiye_groups_persons", "ghafiye_groups_persons.person=ghafiye_persons.id", "INNER");
			db::join('ghafiye_groups_titles', 'ghafiye_groups_titles.group_id=ghafiye_groups_persons.group_id', 'INNER');
			$parenthesis = new parenthesis();
			$parenthesis->where("ghafiye_persons_names.name", $data['word'], "contains");
			$parenthesis->where("ghafiye_songs_persons.role", song\person::singer);
			$person->where($parenthesis);
			$parenthesis = new parenthesis();
			$parenthesis->where("ghafiye_groups_titles.title", $data['word'], "contains");
			$person->orWhere($parenthesis);
			$person->where("ghafiye_songs.status", song::publish);
			db::groupBy('ghafiye_persons.id');
			db::setQueryOption("DISTINCT");
			$persons = $person->get(null, array("ghafiye_persons.*", "ghafiye_persons_names.lang as `showing_lang`"));
;
			$group = new group();
			db::join('ghafiye_songs', 'ghafiye_songs.group=ghafiye_groups.id', 'INNER');
			db::join('ghafiye_groups_titles', 'ghafiye_groups_titles.group_id=ghafiye_groups.id', 'INNER');
			$group->where('ghafiye_songs.status', song::publish);
			$group->where("ghafiye_groups_titles.title", $data['word'], "contains");
			db::groupBy('ghafiye_groups.id');
			db::setQueryOption("DISTINCT");
			$groups = $group->get(null, array("ghafiye_groups.*", "ghafiye_groups_titles.lang as `showing_lang`"));
			$persons = array_merge($groups, $persons);

			if(isset($data['type']) and $data['type']){
				switch($data['type']) {
					case("songs"):
						$view->setSongs($songsByTitle);
						break;
					case("lyrics"):
						$view->setSongs($songsByLyrice);
						break;
					case("persons"):
						$view->setPersons($persons);
						break;
				}
				$view->setType($data['type']);
			}else{
				$view->setSongs(array_values($songs));
				$view->setPersons($persons);
			}
			$results = array(
				"songs" => empty($songsByTitle),
				"persons" => empty($persons),
				"lyrics" => empty($songsByLyrice)
			);
			$view->setResults($results);
			$view->setWord($data['word']);
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$this->response->setView($view);
		return $this->response;
	}
}
