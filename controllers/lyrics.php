<?php
namespace packages\ghafiye\controllers;
use packages\base;
use packages\base\{db, date, http, NotFound, db\parenthesis, inputValidation, views\FormError, response};
use packages\ghafiye\{view, views, controller, group, group\title as groupTitle, person, person\name as personName, song, song\title as songTitle, song\lyric, song\title, authentication};

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
		$lyric->where("status", lyric::published);
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
			if (authentication::check()) {
				$like->user = authentication::getID();
			}
			$like->save();
			$song->likes++;
			$this->response->setData(true, "liked");
		}
		$song->save();
		$this->response->setStatus(true);
		return $this->response;
	}
	public function addComment($data): response {
		$this->response->setStatus(false);
		$view = view::byName(views\lyrics\view::class);
		$song = new song();
		$song->where("status", song::publish);
		$song->where("id", $data["song"]);
		if (!$song = $song->getOne()) {
			throw new NotFound;
		}
		$this->response->setView($view);
		$inputRules = array(
			"name" => array(
				"type" => "string",
			),
			"email" => array(
				"type" => "email",
			),
			"content" => array(
				"type" => "string",
			),
			"reply" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
		);
		$inputs = $this->checkinputs($inputRules);
		if (isset($inputs["reply"])) {
			if ($inputs["reply"]) {
				if (!song\Comment::where("song", $song->id)->where("id", $inputs["reply"])->has()) {
					unset($inputs["reply"]);
				}
			} else {
				unset($inputs["reply"]);
			}
		}
		$comment = new song\Comment();
		$comment->name = $inputs["name"];
		$comment->email = $inputs["email"];
		$comment->content = $inputs["content"];
		$comment->sent_at = date::time();
		$comment->song = $song->id;
		if (authentication::check()) {
			$comment->user = authentication::getID();
		}
		$comment->status = song\Comment::waitForAccept;
		if (isset($inputs["reply"])) {
			$comment->reply = $inputs["reply"];
		}
		$comment->save();
		$this->response->setStatus(true);
		$view->setDataForm($this->inputsvalue($inputRules));
		return $this->response;
	}
	public function description($data): response {
		$this->response->setStatus(false);
		db::join("ghafiye_songs_lyrices", "ghafiye_songs_lyrices.id=ghafiye_songs_lyrices_description.lyric", "INNER");
		db::join("ghafiye_songs", "ghafiye_songs.id=ghafiye_songs_lyrices.song", "INNER");
		$description = new song\lyric\Description();
		$description->where("ghafiye_songs.status", song::publish);
		$description->where("ghafiye_songs_lyrices.id", $data["lyric"]);
		$description->where("ghafiye_songs_lyrices_description.status", song\lyric\Description::accepted);
		$description->orderBy("ghafiye_songs_lyrices_description.sent_at", "DESC");
		if ($descriptions = $description->get(null, "ghafiye_songs_lyrices_description.*")) {
			$items = [];
			foreach ($descriptions as $description) {
				$parenthesis = new parenthesis();
				$parenthesis->where("ip", http::$client["ip"]);
				$parenthesis->where("cookie", http::$request["cookies"]["like"], "=", "OR");
				$isLike = song\lyric\description\Like::where($parenthesis)->where("description", $description->id)->has();
				$items[] = array(
					"id" => $description->id,
					"likes" => $description->likes,
					"text" => nl2br($description->text),
					"isLike" => $isLike,
					"user" => array(
						"id" => $description->user->id,
						"name" => $description->user->getFullName(),
					),
					"sent_at" => array(
						"relative" => date::relativeTime($description->sent_at),
						"date" => date::format("Y/m/d H:i", $description->sent_at),
					)
				);
			}
			$this->response->setData($items, "items");
			$this->response->setStatus(true);
		}
		return $this->response;
	}
	public function addDescription($data): response {
		if (!authentication::check()) {
			throw new NotFound();
		}
		db::join("ghafiye_songs", "ghafiye_songs.id=ghafiye_songs_lyrices.song", "INNER");
		$lyric = new song\lyric();
		$lyric->where("ghafiye_songs.status", song::publish);
		$lyric->where("ghafiye_songs_lyrices.id", $data["lyric"]);
		$lyric = $lyric->getOne("ghafiye_songs_lyrices.*");
		if (!$lyric) {
			throw new NotFound();
		}
		$inputRules = array(
			"content" => array(
				"type" => "string",
			),
		);
		$inputs = $this->checkinputs($inputRules);
		$description = new song\lyric\Description();
		$description->lyric = $lyric->id;
		$description->text = $inputs["content"];
		$description->user = authentication::getID();
		$description->save();
		$this->response->setStatus(true);
		return $this->response;
	}
	public function likeDescription($data): response {
		$description = new song\lyric\Description();
		$description->where("status", song\lyric\Description::accepted);
		$description->where("id", $data["description"]);
		$description = $description->getOne();
		if (!$description) {
			throw new NotFound();
		}
		if (!isset(http::$request["cookies"]["like"])) {
			$cookie = md5("like" . (time()+5756858) . "salam" . rand(0,100));
			http::setcookie("like", $cookie, time() + (86400*365), "/");
			http::$request["cookies"]["like"] = $cookie;
		}
		$parenthesis = new parenthesis();
		$parenthesis->where("ip", http::$client["ip"]);
		$parenthesis->where("cookie", http::$request["cookies"]["like"], "=", "OR");
		$hasLike = song\lyric\description\Like::where($parenthesis)->where("description", $description->id)->getOne();
		if ($hasLike) {
			$hasLike->delete();
			$description->likes--;
			$description->save();
			$this->response->setData(false, "isLiked");
		} else {
			$like = new song\lyric\description\Like();
			$like->description = $description->id;
			$like->cookie = http::$request["cookies"]["like"];
			$like->ip = http::$client["ip"];
			$like->save();
			$description->likes++;
			$description->save();
			$this->response->setData(true, "isLiked");
		}
		$this->response->setData($description->likes, "likes");
		$this->response->setStatus(true);
		return $this->response;
	}
}
