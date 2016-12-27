<?php
namespace packages\ghafiye;
use packages\base\db;
use packages\base\db\dbObject;
use packages\ghafiye\translator\title;
use packages\ghafiye\song\person as songPerson;
class song extends dbObject{
	use title;
	const publish = 1;
	const draft = 2;
	protected $dbTable = "ghafiye_songs";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'musixmatch_id' => array('type' => 'int', 'unique' => true),
		'spotify_id' => array('type' => 'text', 'unique' => true),
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
		'videoss' => array("hasMany", "packages\\ghafiye\\song\\videos", "song"),
        'album' => array("hasOne", "packages\\ghafiye\\album", "album"),
        //'group' => array("hasOne", "packages\\ghafiye\\group", "group"),
        'genre' => array("hasOne", "packages\\ghafiye\\genre", "genre"),
    );
	public function getPerson($role){
		foreach($this->persons as $person){
			if($person->role == $role and $person->primary){
				$personObj = new person;
				return $personObj->byId($person->person);
			}
		}
		foreach($this->persons as $person){
			if($person->role == $role){
				$personObj = new person;
				return $personObj->byId($person->person);
			}
		}
		return null;
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
	static function bySinger(person $singer){
		$songs = array();
		db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "inner");
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.person", $singer->id);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.role", songPerson::singer);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.primary", true);
		foreach(db::get("ghafiye_songs", null,"ghafiye_songs.*") as $data){
			$songs[] = new song($data);
		}
		return $songs;
	}
}
