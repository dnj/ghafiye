<?php
namespace packages\ghafiye;
use packages\base\db;
use packages\base\db\dbObject;
use packages\ghafiye\translator\title;
use packages\ghafiye\song\person as songPerson;
class album extends dbObject{
	use title;
	protected $dbTable = "ghafiye_albums";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'musixmatch_id' => array('type' => 'int', 'unique'=>true),
        'image' => array('type' => 'text')
	);
    protected $relations = array(
		'titles' => array("hasMany", "packages\\ghafiye\\album\\title", "album"),
		'songs' => array("hasMany", "packages\\ghafiye\\song", "album"),
    );
	static function bySinger(person $singer, $limit = null){
		$albums = array();
		db::join("ghafiye_songs", "ghafiye_songs.album=ghafiye_albums.id", "inner");
		db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "inner");
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.person", $singer->id);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.role", songPerson::singer);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.primary", true);
		foreach(db::get("ghafiye_albums", $limit,"ghafiye_albums.*") as $data){
			$albums[] = new self($data);
		}
		return $albums;
	}
	static function bySingerAndTitle(person $singer, $title){
		db::join("ghafiye_albums_titles", "ghafiye_albums_titles.album=ghafiye_albums.id", "inner");
		db::joinWhere("ghafiye_albums_titles", "ghafiye_albums_titles.title", $title);

		db::join("ghafiye_songs", "ghafiye_songs.album=ghafiye_albums.id", "inner");
		db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "inner");
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.person", $singer->id);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.role", songPerson::singer);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.primary", true);
		$data = db::getOne("ghafiye_albums","ghafiye_albums.*");
		if(!$data){
			return null;
		}
		return new self($data);
	}
}
