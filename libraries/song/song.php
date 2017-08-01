<?php
namespace packages\ghafiye;
use packages\base\db;
use packages\userpanel\date;
use packages\base\db\dbObject;
use packages\ghafiye\translator\title;
use packages\ghafiye\song\person as songPerson;
use packages\ghafiye\song\lyric;
class song extends dbObject{
	use title;
	const publish = 1;
	const draft = 2;
	protected $dbTable = "ghafiye_songs";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'musixmatch_id' => array('type' => 'int', 'unique' => true),
		'spotify_id' => array('type' => 'text'),
		'album' => array('type' => 'int'),
		'group' => array('type' => 'int'),
        'release_at' => array('type' => 'int', 'required' => true),
        'update_at' => array('type' => 'int'),
        'duration' => array('type' => 'int', 'required' => true),
        'genre' => array('type' => 'int', 'required' => false),
        'lang' => array('type' => 'text', 'required' => true),
        'image' => array('type' => 'text'),
        'views' => array('type' => 'int'),
        'likes' => array('type' => 'int'),
        'status' => array('type' => 'int', 'required' => true)
	);
    protected $relations = array(
		'titles' => array("hasMany", "packages\\ghafiye\\song\\title", "song"),
		'persons' => array("hasMany", "packages\\ghafiye\\song\\person", "song"),
		'like' => array("hasMany", "packages\\ghafiye\\song\\like", "song"),
		'videoss' => array("hasMany", "packages\\ghafiye\\song\\videos", "song"),
		'lyrics' => array("hasMany", "packages\\ghafiye\\song\\lyric", "song"),
        'album' => array("hasOne", "packages\\ghafiye\\album", "album"),
        'group' => array("hasOne", "packages\\ghafiye\\group", "group"),
        'genre' => array("hasOne", "packages\\ghafiye\\genre", "genre"),
    );
	public function getPerson($role){
		foreach($this->persons as $person){
			if($person->role == $role and $person->primary){
				return $person->person;
			}
		}
		foreach($this->persons as $person){
			if($person->role == $role){
				return $person->person;
			}
		}
		return null;
	}
	public function getLyricByLang($lang = null){
		if(!$lang){
			$lang = $this->lang;
		}
		$lyric = new lyric();
		$lyric->where("lang", $lang);
		$lyric->where("song", $this->id);
		return $lyric->get();
	}
	static function bySingerAndTitle(person $singer, $title){
		db::join("ghafiye_songs_titles", "ghafiye_songs_titles.song=ghafiye_songs.id", "inner");
		db::joinWhere("ghafiye_songs_titles", "ghafiye_songs_titles.title", $title);

		db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "inner");
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.person", $singer->id);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.role", songPerson::singer);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.primary", true);
		$data = db::getOne("ghafiye_songs", "ghafiye_songs.*");
		if(!$data){
			return null;
		}
		return new song($data);
	}
	static function bySinger(person $singer, $limit = null){
		$songs = array();
		db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "inner");
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.person", $singer->id);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.role", songPerson::singer);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.primary", true);
		foreach(db::get("ghafiye_songs", $limit,"ghafiye_songs.*") as $data){
			$songs[] = new song($data);
		}
		return $songs;
	}
	protected function preLoad($data){
		if(!isset($data['release_at'])){
			$data['release_at'] = date::time();
		}
		return $data;
	}
}
