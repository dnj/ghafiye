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

use \packages\ghafiye\views\artists\albums as albumsView;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\formTrait;
class albums extends albumsView{
	use viewTrait;
	protected $artist;
	function __beforeLoad(){
		$this->artist = $this->getArtist();
		$this->setTitle($this->artist->name($this->getSongLanguage()));
		$this->addBodyClass('artist');
		$this->addBodyClass('albums');
	}
	protected function albumImage(album $album){
		return packages::package('ghafiye')->url($album->image ? $album->image : options::get('packages.ghafiye.album.default-image'));
	}
	protected function getAlbumReleaseDate(album $album){
		db::where("album", $album->id);
		db::where("status", song::publish);
		db::orderby('release_at', 'desc');
		return db::getValue('ghafiye_songs', 'release_at');
	}
	protected function getCoverURL(){
		return packages::package('ghafiye')->url($this->artist->cover);
	}
	protected function getAvatarURL(){
		return $this->artist->avatar ? packages::package('ghafiye')->url($this->artist->avatar) : theme::url("dest/images/avatar-placeholder.png");
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
