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
use \packages\ghafiye\album;
use \packages\ghafiye\album\title as albumTitle;

class artists extends controller{
	public function view($data){
		$view = view::byName("\\packages\\ghafiye\\views\\artists\\view");
		$data['artist'] = person::decodeName($data['artist']);
		$personName = personName::byName($data['artist']);
		if(!$personName){
			throw new NotFound;
		}
		$person = person::byId($personName->person);
		$songs = song::bySinger($person);
		if(!$songs){
			throw new NotFound;
		}
		$albums = album::bySinger($person, 5);
		$view->setArtist($person);
		$view->setSongs($songs);
		$view->setAlbums($albums);
		$view->setSongLanguage($personName->lang);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function albums($data){
		$view = view::byName("\\packages\\ghafiye\\views\\artists\\albums");
		$data['artist'] = person::decodeName($data['artist']);
		$personName = personName::byName($data['artist']);
		if(!$personName){
			throw new NotFound;
		}
		$person = person::byId($personName->person);
		$albums = album::bySinger($person);
		$view->setArtist($person);
		$view->setAlbums($albums);
		$view->setSongLanguage($personName->lang);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function album($data){
		$view = view::byName("\\packages\\ghafiye\\views\\artists\\album");
		$data['artist'] = person::decodeName($data['artist']);
		$data['album'] = song::decodeTitle($data['album']);
		$personName = personName::byName($data['artist']);
		if(!$personName){
			throw new NotFound;
		}
		$person = person::byId($personName->person);
		$album = album::bySingerAndTitle($person, $data['album']);
		$view->setArtist($person);
		$view->setAlbum($album);
		$view->setSongLanguage($personName->lang);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
}
