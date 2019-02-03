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

use \packages\ghafiye\group;
use \packages\ghafiye\group\title as groupTitle;
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
		if(!$personName and !$groupName = groupTitle::byTitle($data['artist'])){
			throw new NotFound();
		}
		$person = $group = $song = null;
		if($personName){
			$person = person::byId($personName->person);
			$songs = song::where("status", [song::publish, song::Block], "in")->bySinger($person);
		}else{
			$group = group::byId($groupName->group_id);
			$songs = song::where("status", [song::publish, song::Block], "in")->byGroup($group);
		}
		if($person){
			$albums = album::where("ghafiye_songs.status", [song::publish, song::Block], "in")->bySinger($person, 5);
			$view->setArtist($person);
		}else{
			$albums = album::where("ghafiye_songs.status", [song::publish, song::Block], "in")->byGroup($group, 5);
			$view->setGroup($group);
		}
		$view->setSongs($songs);
		$view->setAlbums($albums);
		$view->setSongLanguage($personName ? $personName->lang : $groupName->lang);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function albums($data){
		$view = view::byName("\\packages\\ghafiye\\views\\artists\\albums");
		$data['artist'] = person::decodeName($data['artist']);
		$personName = personName::byName($data['artist']);
		if(!$personName and !$groupName = groupTitle::byTitle($data['artist'])){
			throw new NotFound();
		}
		if($personName){
			$person = person::byId($personName->person);
			$albums = album::where("ghafiye_songs.status", [song::publish, song::Block], "in")->bySinger($person);
			$view->setArtist($person);
			$view->setSongLanguage($personName->lang);
		}else{
			$group = group::byId($groupName->group_id);
			$albums = album::where("ghafiye_songs.status", [song::publish, song::Block], "in")->byGroup($group);
			$view->setSongLanguage($groupName->lang);
			$view->setGroup($group);
		}
		$view->setAlbums($albums);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function album($data){
		$view = view::byName("\\packages\\ghafiye\\views\\artists\\album");
		$data['artist'] = person::decodeName($data['artist']);
		$data['album'] = song::decodeTitle($data['album']);
		$personName = personName::byName($data['artist']);
		if(!$personName and !$groupName = groupTitle::byTitle($data['artist'])){
			throw new NotFound();
		}
		if($personName){
			$person = person::byId($personName->person);
			$album = album::where("ghafiye_songs.status", [song::publish, song::Block], "in")->bySingerAndTitle($person, $data['album']);
			$albums = album::where("ghafiye_songs.status", [song::publish, song::Block], "in")->bySinger($person, 5);
			$view->setArtist($person);
			$view->setSongLanguage($personName->lang);
		}else{
			$group = group::byId($groupName->group_id);
			$album = album::where("ghafiye_songs.status", [song::publish, song::Block], "in")->byGroupAndTitle($group, $data['album']);
			$albums = album::where("ghafiye_songs.status", [song::publish, song::Block], "in")->byGroup($group, 5);
			$view->setGroup($group);
			$view->setSongLanguage($groupName->lang);
		}
		if(!$album){
			throw new NotFound;
		}
		$view->setAlbum($album);
		$view->setAlbums($albums);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
}
