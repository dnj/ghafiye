<?php
namespace packages\ghafiye\listeners;
use \packages\base\db;
use \packages\base\db\parenthesis;
use \packages\base\translator;

use \packages\userpanel;
use \packages\userpanel\date;
use \packages\userpanel\events\search as event;
use \packages\userpanel\search as saerchHandler;
use \packages\userpanel\search\link;

use \packages\ghafiye\song;
use \packages\ghafiye\group;
use \packages\ghafiye\album;
use \packages\ghafiye\genre;
use \packages\ghafiye\person;
use \packages\ghafiye\authorization;
use \packages\ghafiye\song\person as songPerson;

class search{
	private $abumAccess;
	private $genreAccess;
	private $groupAccess;
	private $personAccess;
	private $songAccess;
	public function find(event $e){
		$this->abumAccess = authorization::is_accessed('albums_list');
		$this->genreAccess = authorization::is_accessed('genres_list');
		$this->groupAccess = authorization::is_accessed('groups_list');
		$this->personAccess = authorization::is_accessed('persons_list');
		$this->songAccess = authorization::is_accessed('songs_list');
		if($this->personAccess){
			$this->persons($e->word);
		}
		if($this->genreAccess){
			$this->genres($e->word);
		}
		if($this->groupAccess){
			$this->groups($e->word);
		}
		if($this->abumAccess){
			$this->albums($e->word);
		}
		if($this->songAccess){
			$this->songs($e->word);
		}
	}
	public function persons($word){
		db::join("ghafiye_persons_names", "ghafiye_persons_names.person=ghafiye_persons.id", "LEFT");
		$parenthesis = new parenthesis();
		$parenthesis->where("ghafiye_persons_names.name", $word, 'contains', 'OR');
		foreach(array('first_name', 'middle_name', 'last_name', 'name_suffix', 'musixmatch_id') as $item){
			$parenthesis->where("ghafiye_persons.{$item}", $word, 'contains', 'OR');
		}
		db::where($parenthesis);
		db::setQueryOption("DISTINCT");
		$persons = [];
		foreach(db::get('ghafiye_persons', null, array('ghafiye_persons.*')) as $person){
			$persons[] = new person($person);
		}
		foreach($persons as $person){
			$result = new link();
			$result->setLink(userpanel\url('persons', array("id" => $person->id)));
			$result->setTitle(translator::trans("ghafiye.search.persons", array(
				'name' => $person->name()
			)));
			saerchHandler::addResult($result);
		}
	}
	public function songs($word){
		db::join("ghafiye_songs_titles", "ghafiye_songs_titles.song=ghafiye_songs.id", "LEFT");
		if($this->personAccess){
			db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "LEFT");
			db::join("ghafiye_persons", "ghafiye_persons.id=ghafiye_songs_persons.person", "LEFT");
			db::join("ghafiye_persons_names", "ghafiye_persons_names.person=ghafiye_persons.id", "LEFT");
		}
		if($this->genreAccess){
			db::join("ghafiye_genres", "ghafiye_genres.id=ghafiye_songs.genre", "LEFT");
			db::join("ghafiye_genres_titles", "ghafiye_genres_titles.genre=ghafiye_genres.id", "LEFT");
		}
		if($this->abumAccess){
			db::join("ghafiye_albums", "ghafiye_albums.id=ghafiye_songs.album", "LEFT");
			db::join("ghafiye_albums_titles", "ghafiye_albums_titles.album=ghafiye_albums.id", "LEFT");
		}
		if($this->groupAccess){
			db::join("ghafiye_groups", "ghafiye_groups.id=ghafiye_songs.`group`", "LEFT");
			db::join("ghafiye_groups_titles", "ghafiye_groups_titles.group_id=ghafiye_groups.id", "LEFT");
		}
		$parenthesis = new parenthesis();
		$parenthesis->where("ghafiye_songs_titles.title", $word, 'contains', 'OR');
		if($this->genreAccess){
			$parenthesis->where("ghafiye_genres_titles.title", $word, 'contains', 'OR');
		}
		if($this->abumAccess){
			$parenthesis->where("ghafiye_albums.musixmatch_id", $word, 'contains', 'OR');
			$parenthesis->where("ghafiye_albums_titles.title", $word, 'contains', 'OR');
		}
		if($this->groupAccess){
			$parenthesis->where("ghafiye_groups_titles.title", $word, 'contains', 'OR');
		}
		if($this->personAccess){
			$parenthesis->where("ghafiye_persons_names.name", $word, 'contains', 'OR');
			foreach(array('first_name', 'middle_name', 'last_name', 'name_suffix', 'musixmatch_id') as $item){
				$parenthesis->where("ghafiye_persons.{$item}", $word, 'contains', 'OR');
			}
		}
		foreach(array('spotify_id', 'musixmatch_id') as $item){
			$parenthesis->where("ghafiye_songs.{$item}", $word, 'contains', 'OR');
		}
		db::where($parenthesis);
		db::setQueryOption("DISTINCT");
		$songs = [];
		$songData = db::get('ghafiye_songs', null, array('ghafiye_songs.*'));
		foreach($songData as $song){
			$songs[] = new song($song);
		}
		foreach($songs as $song){
			$result = new link();
			$result->setLink(userpanel\url('songs', array("id" => $song->id)));
			$result->setTitle(translator::trans("ghafiye.search.songs", array(
				'title' => $song->title()
			)));
			$result->setDescription(translator::trans("ghafiye.search.songs.description", array(
				'release_at' => date::format("Y/m/d H:i:s", $song->release_at),
				'singer' => $song->getPerson(songPerson::singer)->name(),
				'genre' => $song->genre->title()
			)));
			saerchHandler::addResult($result);
		}
	}
	public function groups($word){
		db::join("ghafiye_groups_titles", "ghafiye_groups_titles.group_id=ghafiye_groups.id", "LEFT");
		db::join("ghafiye_groups_persons", "ghafiye_groups_persons.group_id=ghafiye_groups.id", "LEFT");
		db::join("ghafiye_persons", "ghafiye_persons.id=ghafiye_groups_persons.person", "INNER");
		db::join("ghafiye_persons_names", "ghafiye_persons_names.person=ghafiye_persons.id", "INNER");
		$parenthesis = new parenthesis();
		$parenthesis->where("ghafiye_groups_titles.title", $word, 'contains', 'OR');
		$parenthesis->where("ghafiye_persons_names.name", $word, 'contains', 'OR');
		foreach(array('first_name', 'middle_name', 'last_name', 'name_suffix', 'musixmatch_id') as $item){
			$parenthesis->where("ghafiye_persons.{$item}", $word, 'contains', 'OR');
		}
		db::where($parenthesis);
		db::setQueryOption("DISTINCT");
		$groups = [];
		foreach(db::get('ghafiye_groups', null, array('ghafiye_groups.*')) as $group){
			$groups[] = new group($group);
		}
		foreach($groups as $group){
			$result = new link();
			$result->setLink(userpanel\url('groups', array("id" => $group->id)));
			$result->setTitle(translator::trans("ghafiye.search.groups", array(
				'title' => $group->title()
			)));
			saerchHandler::addResult($result);
		}
	}
	public function albums($word){
		db::join("ghafiye_albums_titles", "ghafiye_albums_titles.album=ghafiye_albums.id", "LEFT");
		$parenthesis = new parenthesis();
		$parenthesis->where("ghafiye_albums.musixmatch_id", $word, 'contains', 'OR');
		$parenthesis->where("ghafiye_albums_titles.title", $word, 'contains', 'OR');
		db::where($parenthesis);
		db::setQueryOption("DISTINCT");
		$albums = [];
		foreach(db::get('ghafiye_albums', null, array('ghafiye_albums.*')) as $album){
			$albums[] = new album($album);
		}
		foreach($albums as $album){
			$result = new link();
			$result->setLink(userpanel\url('albums', array("id" => $album->id)));
			$result->setTitle(translator::trans("ghafiye.search.albums", array(
				'title' => $album->title()
			)));
			saerchHandler::addResult($result);
		}
	}
	public function genres($word){
		db::join("ghafiye_genres_titles", "ghafiye_genres_titles.genre=ghafiye_genres.id", "LEFT");
		$parenthesis = new parenthesis();
		$parenthesis->where("ghafiye_genres.musixmatch_id", $word, 'contains', 'OR');
		$parenthesis->where("ghafiye_genres_titles.title", $word, 'contains', 'OR');
		db::where($parenthesis);
		db::setQueryOption("DISTINCT");
		$genres = [];
		foreach(db::get('ghafiye_genres', null, array('ghafiye_genres.*')) as $genre){
			$genres[] = new genre($genre);
		}
		foreach($genres as $genre){
			$result = new link();
			$result->setLink(userpanel\url('genres', array("id" => $genre->id)));
			$result->setTitle(translator::trans("ghafiye.search.genres", array(
				'title' => $genre->title()
			)));
			saerchHandler::addResult($result);
		}
	}
}
