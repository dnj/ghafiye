<?php
namespace packages\ghafiye\controllers;
use \packages\base;
use \packages\base\NotFound;

use \packages\ghafiye\controller;
use \packages\ghafiye\view;

use \packages\ghafiye\song;
use \packages\ghafiye\genre;
use \packages\ghafiye\genre\title as genreTitle;

class explore extends controller{
	public function best(){
		$view = view::byName("\\packages\\ghafiye\\views\\explore\\best");
		$song = new song();
		$song->where("status", song::publish);
		$song->orderBy("views", "desc");
		$view->setSongs($song->get(40));
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function lastest(){
		$view = view::byName("\\packages\\ghafiye\\views\\explore\\lastest");
		$song = new song();
		$song->where("status", song::publish);
		$song->orderBy("release_at", "desc");
		$view->setSongs($song->get(40));
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function genre($data){
		$view = view::byName("\\packages\\ghafiye\\views\\explore\\genre");
		$data['genre'] = genre::decodeTitle($data['genre']);
		$genreTitle = genreTitle::byTitle($data['genre']);
		if(!$genreTitle){
			throw new NotFound;
		}
		$genre = genre::byId($genreTitle->genre);
		$song = new song();
		$song->where("genre", $genre->id);
		$song->where("status", song::publish);
		$song->orderBy("views", "desc");

		$view->setGenre($genre);
		$view->setSongs($song->get(40));
		$view->setSongLanguage($genreTitle->lang);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
}
