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
use \packages\ghafiye\song;
use \packages\ghafiye\song\lyric;
use \packages\ghafiye\genre;
use \packages\ghafiye\album;
use \packages\sitemap\item;



class sitemap extends controller{
	public function import(){
		return array_merge($this->export_atrists(), $this->export_songs(), $this->export_genres(), $this->export_albums());
	}
	private function export_atrists(){
		db::join("ghafiye_songs_persons", "ghafiye_songs_persons.person=ghafiye_persons.id", "inner");
		db::join("ghafiye_songs", "ghafiye_songs.id=ghafiye_songs_persons.song", "inner");

		db::where("ghafiye_songs_persons.role", song\person::singer);
		db::where("ghafiye_songs.status", song::publish);
		db::setQueryOption('DISTINCT');

		$personsData = db::get("ghafiye_persons", null, array("ghafiye_persons.*"));
		$persons = array();
		foreach($personsData as $personData){
			$persons[] = new person($personData);
		}
		foreach($persons as $person){
			foreach($person->names as $name){
				$item = new item();
				$item->setURI(base\url($person->encodedName($name->lang), array(), true));
				$item->SetChangeFreq(item::weekly);
				$item->setPriority(0.5);
				$items[] = $item;
			}
		}
		return $items;
	}
	private function export_songs(){
		db::where("ghafiye_songs.status", song::publish);
		$songsData = db::get("ghafiye_songs", null, array("ghafiye_songs.*"));
		$songs = array();
		foreach($songsData as $songData){
			$songs[] = new song($songData);
		}
		foreach($songs as $song){
			db::where("song", $song->id);
			$langs = array_column(db::get("ghafiye_songs_lyrices",null, "DISTINCT `lang`"), 'lang');
			$singer = $song->getPerson(song\person::singer);
			foreach($langs as $lang){
				$item = new item();
				$item->setURI(base\url($singer->encodedName($lang).'/'.$song->encodedTitle($lang), array(), true));
				$item->SetChangeFreq(item::monthly);
				$item->setLastModified(max($song->release_at, $song->update_at));
				$item->setPriority(0.65);
				$items[] = $item;
			}
		}
		return $items;
	}
	private function export_genres(){
		db::join("ghafiye_songs", "ghafiye_songs.genre=ghafiye_genres.id", "inner");
		db::where("ghafiye_songs.status", song::publish);
		db::setQueryOption('DISTINCT');
		$genresData = db::get("ghafiye_genres", null, array("ghafiye_genres.*"));
		$genres = array();
		foreach($genresData as $genreData){
			$genres[] = new genre($genreData);
		}
		foreach($genres as $genre){
			foreach($genre->titles as $title){
				$item = new item();
				$item->setURI(base\url('explore/genre/'.$genre->encodedTitle($title->lang), array(), true));
				$item->SetChangeFreq(item::daily);
				$item->setPriority(0.75);
				$items[] = $item;
			}
		}
		return $items;
	}
	private function export_albums(){
		db::join("ghafiye_songs", "ghafiye_songs.album=ghafiye_albums.id", "inner");
		db::where("ghafiye_songs.status", song::publish);
		db::setQueryOption('DISTINCT');
		$albumsData = db::get("ghafiye_albums", null, array("ghafiye_albums.*"));
		$albums = array();
		foreach($albumsData as $albumData){
			$albums[] = new album($albumData);
		}
		foreach($albums as $album){
			db::join("ghafiye_songs_persons", "ghafiye_songs_persons.person=ghafiye_persons.id", "inner");
			db::join("ghafiye_songs", "ghafiye_songs.id=ghafiye_songs_persons.song", "inner");
			db::where("ghafiye_songs.album", $album->id);
			db::where("ghafiye_songs.status", song::publish);
			db::where("ghafiye_songs_persons.role", song\person::singer);
			$singerData = db::getOne("ghafiye_persons", "ghafiye_persons.*");
			$singer = new person($singerData);
			foreach($album->titles as $title){
				$item = new item();
				$item->setURI(base\url($singer->encodedName($title->lang).'/albums/'.$album->encodedTitle($title->lang), array(), true));
				$item->SetChangeFreq(item::monthly);
				$item->setPriority(0.4);
				$items[] = $item;
			}
		}
		return $items;
	}
}
