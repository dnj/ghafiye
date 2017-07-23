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
		return packages::package('ghafiye')->url($song->image ? $song->image : base\options::get('packages.ghafiye.song.default-image'));
	}
	protected function numberOfLangs(){
		db::where("song", $this->song->id);
		return db::getValue("ghafiye_songs_lyrices", "count(DISTINCT `lang`)");
	}
	protected function getLangs(){
		db::where("song", $this->song->id);
		db::where('lang', [$this->song->lang, 'fa'], 'in');
		return array_column(db::get("ghafiye_songs_titles",null, "ghafiye_songs_titles.*"), 'lang');
	}
	protected function isLang(string $language):bool{
		db::where("song", $this->song->id);
		db::where('lang', $language);
		return db::has("ghafiye_songs_titles");
	}
	protected function is_ltr($lang){
		return !in_array($lang, array('ar','fa','dv','he','ps','sd','ur','yi','ug','ku'));
	}
	protected function getSongs(){
		if($album = $this->song->album){
			$song = new song();
			$song->where("ghafiye_songs.status", song::publish);
			$song->where('ghafiye_songs.id', $this->song->id, '!=');
			$song->where('ghafiye_songs.album', $album->id);
			$song->orderBy("ghafiye_songs.release_at", "desc");
			return $song->get(4, 'ghafiye_songs.*');
		}
	}
	protected function isMoreSong():bool{
		$album = $this->song->album;
		$song = new song();
		$song->where("ghafiye_songs.status", song::publish);
		$song->where('ghafiye_songs.id', $this->song->id, '!=');
		$song->where('ghafiye_songs.album', $album->id);
		return $song->count() > 4;
	}
	protected function getShareSocial(){
		$lang = $this->getLyricsLanguage();
		$singer = $this->song->getPerson(person::singer);
		$url = base\url($this->singer->encodedName().'/'.$this->song->encodedTitle(), [], true);
		return [
			[
				"name"=> "facebook",
				"link"=> "http://www.facebook.com/sharer.php?u=".$url
			],
			[
				"name"=> "telegram",
				"link"=> "tg://msg_url?text=".translator::trans('share.song.on.telegram.text', ['title'=>$this->song->title($lang), 'artist'=>$this->singer->name($lang)])."&url=".$url
			],
			[
				"name"=> "twitter",
				"link"=> "http://www.twitter.com/share?url=".$url
			],
			[
				"name"=> "google-plus",
				"link"=> "http://www.plus.google.com/share?url=".$url
			],
			[
				"name"=> "pinterest",
				"link"=> "http://www.pinterest.com/pin/create/button/?url=".$url
			],
			[
				"name"=> "linkedin",
				"link"=> "https://www.linkedin.com/cws/share?url=".$url
			],
			[
				"name"=> "tumblr",
				"link"=> "http://www.tumblr.com/share/link?url=".$url
			],
			[
				"name"=> "vk",
				"link"=> "http://www.vk.com/share.php?url=".$url
			],
			[
				"name"=> "reddit",
				"link"=> "http://www.reddit.com/submit?url=".$url
			],
			[
				"name"=> "mail",
				"link"=> "mailto:?subject={$this->song->title($lang)}&body={$this->song->title($lang)}\n".$url
			]
		];
	}
	protected function getAlbumImage():string{
		return base\packages::package('ghafiye')->url($this->song->album->image ? $this->song->album->image : base\options::get('packages.ghafiye.album.default-image'));
	}
}
