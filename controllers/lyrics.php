<?php
namespace packages\ghafiye\controllers;
use \packages\base;
use \packages\base\db;
use \packages\base\http;
use \packages\base\NotFound;
use \packages\base\inputValidation;
use \packages\base\views\FormError;

use \packages\ghafiye\controller;
use \packages\ghafiye\view;

use \packages\ghafiye\person;
use \packages\ghafiye\person\name as personName;
use \packages\ghafiye\song;
use \packages\ghafiye\song\title as songTitle;
use \packages\ghafiye\song\lyric;

class lyrics extends controller{
	public function view($data){
		$view = view::byName("\\packages\\ghafiye\\views\\lyrics\\view");
		$data['artist'] = person::decodeName($data['artist']);
		$data['song'] = song::decodeTitle($data['song']);
		$personName = personName::byName($data['artist']);
		if(!$personName){
			throw new NotFound;
		}
		$person = person::byId($personName->person);
		$song = song::bySingerAndTitle($person, $data['song']);
		if(!$song){
			throw new NotFound;
		}
		$songTitle = new songTitle();
		$songTitle->where("song", $song->id);
		$songTitle->where("title", $data['song']);
		$songTitle->getOne();
		$lyric = new lyric();
		$lyric->where("song", $song->id);
		$lyric->where("lang", array_unique(array($songTitle->lang)), 'in');
		$lyric->orderby('time', 'asc');
		$lyric->orderby('id', 'asc');
		$lyrices = $lyric->get();
		$view->setSinger($person);
		$view->setSong($song);
		$view->setLyrices($lyrices);
		$view->setLyricsLanguage($songTitle->lang);
		$this->response->setView($view);
		return $this->response;
	}
}
