<?php
namespace packages\ghafiye;
use packages\base;
use packages\userpanel\date;
use packages\base\{db, db\dbObject, db\parenthesis};
use packages\ghafiye\{translator\title, song\person as songPerson, song\lyric};

class song extends dbObject{
	use title, imageTrait;
	const publish = 1;
	const draft = 2;
	const synced = 1;
	protected $singer;
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
		"synced" => array("type" => "int"),
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
		if($this->group){
			return $this->group;
		}
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
	public function getSinger() {
		if (!$this->singer) {
			$this->singer = $this->getPerson(songPerson::singer);
		}
		return $this->singer;
	}
	public function url(): string {
		return base\url($this->getSinger()->encodedName() . '/' . $this->encodedTitle());
	}
	public function translatedTo(string $lang) {
		$translate = new song\Translate();
		$translate->where("song", $this->id);
		$translate->where("lang", $lang);
		$translate->where("progress", 100);
		return $translate->has();
	}
	public function getTranslateProgressByLang(string $lang): int {
		$translate = new song\Translate();
		$translate->where("song", $this->id);
		$translate->where("lang", $lang);
		if ($translate = $translate->getOne()) {
			return $translate->progress;
		}
		return 0;
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
	static function byGroupAndTitle(group $group, $title){
		db::join("ghafiye_songs_titles", "ghafiye_songs_titles.song=ghafiye_songs.id", "inner");
		db::joinWhere("ghafiye_songs_titles", "ghafiye_songs_titles.title", $title);
		
		db::where("ghafiye_songs.group", $group->id);
		$data = db::getOne("ghafiye_songs", "ghafiye_songs.*");
		if(!$data){
			return null;
		}
		return new song($data);
	}
	static function bySinger(person $singer, $limit = null){
		$songs = array();
		db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "LEFT");
		db::join("ghafiye_groups_persons", "ghafiye_groups_persons.group_id=ghafiye_songs.group", "LEFT");

		$parenthesis = new parenthesis();
		$parenthesis->where("ghafiye_songs_persons.person", $singer->id);
		$parenthesis->where("ghafiye_songs_persons.role", songPerson::singer);
		$parenthesis->where("ghafiye_songs_persons.primary", true);
		db::where($parenthesis);
		$parenthesis = new parenthesis();
		$parenthesis->orWhere("ghafiye_groups_persons.person", $singer->id);
		db::orWhere($parenthesis);
		foreach(db::get("ghafiye_songs", $limit, "ghafiye_songs.*") as $data){
			$songs[] = new song($data);
		}
		return $songs;
	}
	static function byGroup(group $group, $limit = null):array{
		$songs = [];
		db::where('ghafiye_songs.group', $group->id);
		foreach(db::get("ghafiye_songs", $limit, "ghafiye_songs.*") as $data){
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
