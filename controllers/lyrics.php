<?php
namespace packages\ghafiye\controllers;
use \packages\base;
use \packages\base\db;
use \packages\base\http;
use \packages\base\NotFound;
use \packages\base\db\parenthesis;
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

class lyrics extends controller{
	public function view($data){
		$view = view::byName("\\packages\\ghafiye\\views\\lyrics\\view");
		$data['artist'] = person::decodeName($data['artist']);
		$data['song'] = song::decodeTitle($data['song']);
		$personName = personName::byName($data['artist']);
		if(!$personName and !$groupName = groupTitle::byTitle($data['artist'])){
			throw new NotFound();
		}
		$person = $group = $song = null;
		if($personName){
			$person = person::byId($personName->person);
			$song = song::where("ghafiye_songs.status", song::publish)->bySingerAndTitle($person, $data['song']);
		}else{
			$group = group::byId($groupName->group_id);
			$song = song::where("ghafiye_songs.status", song::publish)->byGroupAndTitle($group, $data['song']);
		}
		if(!$song){
			throw new NotFound;
		}
		if($personName and $song->group){
			$this->response->Go(base\url("{$song->group->encodedTitle()}/{$song->encodedTitle()}"));
		}
		$song->views++;
		$song->save();
		$songTitle = new songTitle();
		$songTitle->where("song", $song->id);
		$songTitle->where("title", $data['song']);
		$songTitle = $songTitle->getOne();
		$lyric = new lyric();
		$lyric->where("song", $song->id);
		$lyric->where("lang", array_unique(array($songTitle->lang, $song->lang)), 'in');
		$lyric->orderby('time', 'asc');
		$lyric->orderby('id', 'asc');
		$lyrices = $lyric->get();
		$parenthesis = new parenthesis();
		$parenthesis->where("ip", http::$client['ip']);
		if(isset( http::$request['cookies']['like'])){
			$parenthesis->where("cookie", http::$request['cookies']['like'],'=', "OR");
		}
		$songLiked = song\like::where($parenthesis)->where("song", $song->id)->getOne();
		$view->setlikeStatus(($songLiked ? true : false));
		if($person){
			$view->setSinger($person);
		}else{
			$view->setGroup($group);
		}
		$view->setSong($song);
		$view->setLyrices($lyrices);
		$view->setLyricsLanguage($songTitle->lang);
		$this->response->setView($view);
		return $this->response;
	}
	public function likeSong($data){
		$song = song::where("id", $data['song'])->where("status", song::publish)->getOne();
		if(!$song){
			$this->response->setStatus(false);
			throw new NotFound();
		}
		if(!isset(http::$request['cookies']['like'])){
			$cookie = md5("like".(time()+5756858)."salam".rand(0,100));
			http::setcookie("like", $cookie, time() + (86400*365), "/");
			http::$request['cookies']['like'] = $cookie;
		}
		$parenthesis = new parenthesis();
		$parenthesis->where("ip", http::$client['ip']);
		$parenthesis->where("cookie", http::$request['cookies']['like'],'=', "OR");
		$hasLike = song\like::where($parenthesis)->where("song", $song->id)->getOne();
		if($hasLike){
			$hasLike->delete();
			$song->likes--;
			$this->response->setData(false,"liked");
		}else{
			$like = new song\like();
			$like->ip = http::$client['ip'];
			$like->cookie =  http::$request['cookies']['like'];
			$like->song = $song->id;
			$like->save();
			$song->likes++;
			$this->response->setData(true, "liked");
		}
		$song->save();
		$this->response->setStatus(true);
		return $this->response;
	}
}
