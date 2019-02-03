<?php
namespace themes\musixmatch\views;
use \packages\base;
use \packages\base\db;
use \packages\base\translator;
use \packages\base\packages;
use \packages\ghafiye\genre;
use \packages\ghafiye\song;
use \packages\ghafiye\views\index as homepage;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\formTrait;
class index extends homepage{
	use viewTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.homepage.title"));
		$this->addBodyClass('home');
	}
	function getGenres(){
		return genre::getActives(6);
	}
	protected function getTopSongs(){
		$song = new song();
		$song->where("status", [song::publish, song::Block], "in");
		$song->orderBy("views", "desc");
		return $song->get(6);
	}
	protected function getTopSongsByGenre(genre $genre){
		$song = new song();
		$song->where("status", [song::publish, song::Block], "in");
		$song->where("genre", $genre->id);
		$song->orderBy("views", "desc");
		return $song->get(6);
	}
	protected function numberOfLangs(song $song){
		db::where("song", $song->id);
		return db::getValue("ghafiye_songs_lyrices", "count(DISTINCT `lang`)");
	}
	protected function getLastSongs(){
		$song = new song();
		$song->where("status", [song::publish, song::Block], "in");
		$song->orderBy("release_at", "desc");
		return $song->get(4);
	}
}
