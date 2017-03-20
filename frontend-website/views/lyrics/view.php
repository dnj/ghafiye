<?php
namespace themes\musixmatch\views\lyrics;
use \packages\base;
use \packages\base\db;
use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\base\packages;
use \packages\ghafiye\genre;
use \packages\ghafiye\song;
use \packages\ghafiye\song\person;

use \packages\ghafiye\views\lyrics\view as lyricsView;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\formTrait;
class view extends lyricsView{
	use viewTrait;
	protected $song;
	protected $singer;
	function __beforeLoad(){
		$this->song = $this->getSong();
		$this->singer = $this->song->getPerson(person::singer);

		$this->setTitle(array(
			$this->singer->name($this->getLyricsLanguage()),
			$this->song->title($this->getLyricsLanguage())
		));
		$this->addBodyClass('lyric');
	}
	function getGenres(){
		return genre::getActives(6);
	}
	protected function getTopSongs(){
		$song = new song();
		$song->where("status", song::publish);
		$song->orderBy("views", "desc");
		return $song->get(6);
	}
	protected function getTopSongsByGenre(genre $genre){
		$song = new song();
		$song->where("status", song::publish);
		$song->where("genre", $genre->id);
		$song->orderBy("views", "desc");
		return $song->get(6);
	}
	protected function songImage(song $song){
		if($song->image){
			return packages::package('ghafiye')->url($song->image);
		}
		return theme::url('dest/images/song.jpg');
	}
	protected function numberOfLangs(){
		db::where("song", $this->song->id);
		return db::getValue("ghafiye_songs_lyrices", "count(DISTINCT `lang`)");
	}
	protected function langs(){
		db::where("song", $this->song->id);
		return array_column(db::get("ghafiye_songs_lyrices",null, "DISTINCT `lang`"), 'lang');
	}
	protected function is_ltr($lang){
		return !in_array($lang, array('ar','fa','dv','he','ps','sd','ur','yi','ug','ku'));
	}
	protected function getLastSongs(){
		$song = new song();
		$song->where("status", song::publish);
		$song->orderBy("release_at", "desc");
		return $song->get(4);
	}
}
