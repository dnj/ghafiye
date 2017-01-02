<?php
namespace packages\ghafiye\controllers;
use \packages\base;
use \packages\base\db;
use \packages\base\http;
use \packages\base\NotFound;
use \packages\base\inputValidation;
use \packages\base\db\parenthesis;
use \packages\base\views\FormError;

use \packages\ghafiye\song;
use \packages\ghafiye\view;
use \packages\ghafiye\person;
use \packages\ghafiye\person\name as personName;
use \packages\ghafiye\controller;

class search extends controller{
	public function index(){
		$view = view::byName("\\packages\\ghafiye\\views\\search\\index");
		$inputsRules = array(
			'word' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			)
		);
		$this->response->setStatus(true);

		try{
			$inputs = $this->checkinputs($inputsRules);
			if(isset($inputs["word"]) and $inputs["word"]){
				$song = new song();
				$person = new person();
				db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "INNER");
				db::join("ghafiye_persons", "ghafiye_songs_persons.person=ghafiye_persons.id", "INNER");
				db::join("ghafiye_persons_names", "ghafiye_persons_names.person=ghafiye_persons.id", "INNER");
				$song->where("ghafiye_persons_names.name", $inputs["word"], "contains");
				db::setQueryOption("DISTINCT");
				$songsByArtistName = $song::where("status", song::publish)->get(null, array("ghafiye_songs.*", "ghafiye_persons_names.lang as `showing_lang`"));


				db::join("ghafiye_songs_titles", "ghafiye_songs_titles.song=ghafiye_songs.id", "INNER");
				$song->where("ghafiye_songs_titles.title", $inputs["word"], "contains");
				db::setQueryOption("DISTINCT");
				$songsByTitle = $song::where("status", song::publish)->get(null, array("ghafiye_songs.*", "ghafiye_songs_titles.lang as `showing_lang`"));


				db::join("ghafiye_songs_lyrices", "ghafiye_songs_lyrices.song=ghafiye_songs.id", "INNER");
				$song->where("ghafiye_songs_lyrices.text", $inputs["word"], "contains");
				db::setQueryOption("DISTINCT");
				$songsByLyrice = $song::where("status", song::publish)->get(null, array("ghafiye_songs.*", "ghafiye_songs_lyrices.lang as `showing_lang`"));

				$songs = array_merge($songsByArtistName, $songsByTitle, $songsByLyrice);
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
				$person->where("ghafiye_persons_names.name", $inputs["word"], "contains");
				$person->where("ghafiye_songs_persons.role", song\person::singer);
				$person->where("ghafiye_songs.status", song::publish);
				db::setQueryOption("DISTINCT");
				$persons = $person->get(null, array("ghafiye_persons.*", "ghafiye_persons_names.lang as `showing_lang`"));
				$view->setSongs(array_values($songs));
				$view->setPersons($persons);
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$this->response->setView($view);
		return $this->response;
	}
}
