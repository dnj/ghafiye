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
}
