<?php
namespace themes\musixmatch\views\artists;
use \packages\base\db;
use \packages\base\translator;
use \packages\ghafiye\genre;
use \packages\ghafiye\song;
use \packages\ghafiye\album as songAlbum;
use \packages\ghafiye\song\person;
use \packages\ghafiye\album as albumObj;

use \packages\ghafiye\views\artists\album as albumView;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\formTrait;
class album extends albumView{
	use viewTrait;
	protected $artist;
	protected $album;
	function __beforeLoad(){
		$this->artist = $this->getArtist();
		$this->album = $this->getAlbum();
		$this->setTitle($this->artist->name($this->getSongLanguage()));
		$this->addBodyClass('album');
		$this->addMetaTags();
	}
	protected function getAlbumReleaseDate(){
		db::where("album", $this->album->id);
		db::where("status", song::publish);
		db::orderby('release_at', 'desc');
		return db::getValue('ghafiye_songs', 'release_at');
	}
	protected function getMoreAlbums():array{
		$albums = [];
		$i = 0;
		foreach($this->getAlbums() as $album){
			if($album->id != $this->album->id and $i < 5){
				$albums[] = $album;
			}
			$i++;
		}
		return $albums;
	}
	private function addMetaTags(){
		$lang = $this->getSongLanguage();
		$this->addMetaTag(array(
			'property' => 'og:title',
			'content' => translator::trans('ghafiye.album.view.metaTag.og:title', ['album'=>$this->album->title($lang), 'artist'=>$this->artist->name($lang)])
		));
		if($this->album->lang == $lang){
			$this->addMetaTag(array(
				'property' => 'og:description',
				'content' => translator::trans('ghafiye.album.view.metaTag.description', ['album'=>$this->album->title($lang), 'artist'=>$this->artist->name($lang)])
			));
		}else{
			$this->addMetaTag(array(
				'property' => 'og:description',
				'content' => translator::trans('ghafiye.album.view.metaTag.description.translated', ['album'=>$this->album->title($lang), 'artist'=>$this->artist->name($lang)])
			));
		}
		$this->addMetaTag(array(
			'property' => 'og:type',
			'content' => 'album'
		));
		$this->addMetaTag(array(
			'property' => 'og:image',
			'content' => $this->album->getImage(255, 255, 'image', true)
		));
		$this->addMetaTag(array(
			'property' => 'album:artist:name',
			'content' => $this->artist->name($lang)
		));
		foreach($this->getSongs() as $song){
			if($song->status == song::publish){
				$this->addMetaTag(array(
					'property' => 'album:song',
					'content' => $song->title($lang)
				));
			}
		}
		$this->addMetaTag(array(
			'name' => 'twitter:card',
			'content' => 'summary_large_image'
		));
		$this->addMetaTag(array(
			'property' => 'twitter:title',
			'content' => translator::trans('ghafiye.album.view.metaTag.og:title', ['album'=>$this->album->title($lang), 'artist'=>$this->artist->name($lang)])
		));
		if($this->album->lang == $lang){
			$this->addMetaTag(array(
				'property' => 'twitter:description',
				'content' => translator::trans('ghafiye.album.view.metaTag.description', ['album'=>$this->album->title($lang), 'artist'=>$this->artist->name($lang)])
			));
		}else{
			$this->addMetaTag(array(
				'property' => 'twitter:description',
				'content' => translator::trans('ghafiye.album.view.metaTag.description.translated', ['album'=>$this->album->title($lang), 'artist'=>$this->artist->name($lang)])
			));
		}
	}
}
