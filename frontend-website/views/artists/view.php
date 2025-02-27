<?php
namespace themes\musixmatch\views\artists;
use \packages\base\db;
use \packages\base\translator;
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
		$this->artist = $this->getArtist() ? $this->getArtist() : $this->getGroup();
		$this->setTitle($this->artist->name($this->getSongLanguage()));
		$this->addBodyClass('artist');
		$this->setDescription(translator::trans('ghafiye.artistsView.description', [
			'name' => $this->artist->name()
		]));
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
	protected function getAlbumReleaseDate(album $album){
		db::where("album", $album->id);
		db::where("status", [song::publish, song::Block], "in");
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
		$song->where("status", [song::publish, song::Block], "in");
		$song->orderBy("release_at", "desc");
		return $song->get(4);
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
	private function addMetaTags(){
		$this->addMetaTag(array(
			'property' => 'og:description',
			'content' => translator::trans('ghafiye.lyric.view.metaTag.description', ['title'=>$this->song->title($lang), 'artist'=>$this->singer->name($lang)])
		));
	}
}
