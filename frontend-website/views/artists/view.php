<?php
namespace themes\musixmatch\views\artists;
use \packages\base\db;
use \packages\base\options;
use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\base\packages;
use \packages\ghafiye\genre;
use \packages\ghafiye\song;
use \packages\ghafiye\song\person;
use \packages\ghafiye\album;

use \packages\ghafiye\views\artists\view as artistsView;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\formTrait;
class view extends artistsView{
	use viewTrait;
	protected $artist;
	function __beforeLoad(){
		$this->artist = $this->getArtist();
		$this->setTitle($this->artist->name($this->getSongLanguage()));
		$this->addBodyClass('artist');
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
		return packages::package('ghafiye')->url($song->image ? $song->image : options::get('packages.ghafiye.song.default-image'));
	}
	protected function albumImage(album $album){
		return packages::package('ghafiye')->url($album->image ? $album->image: options::get('packages.ghafiye.album.default-image'));
	}
	protected function getAlbumReleaseDate(album $album){
		db::where("album", $album->id);
		db::where("status", song::publish);
		db::orderby('release_at', 'desc');
		return db::getValue('ghafiye_songs', 'release_at');
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
	protected function getCoverURL(){
		return packages::package('ghafiye')->url($this->artist->cover);
	}
	protected function getAvatarURL(){
		return $this->artist->avatar ? packages::package('ghafiye')->url($this->artist->avatar) : theme::url("packages.ghafiye.persons.deafault_image");
	}
	protected function getAristGenres(){
		$genres = array();
		db::join("ghafiye_genres", "ghafiye_genres.id=ghafiye_songs.genre", "inner");

		db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "inner");
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.person", $this->artist->id);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.role", person::singer);
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.primary", true);
		db::groupBy('ghafiye_songs.genre');

		foreach(db::get("ghafiye_songs", null,"ghafiye_genres.*") as $data){
			$genres[] = new genre($data);
		}
		return $genres;
	}
}
