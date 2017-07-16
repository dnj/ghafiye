<?php
namespace packages\ghafiye\views\artists;
use \packages\ghafiye\view;
use \packages\base\db\dbObject;
use \packages\ghafiye\person;
use \packages\ghafiye\album as albumObj;
class album extends view{
	public function setArtist(person $artist){
		$this->setData($artist, 'artist');
	}
	public function getArtist(){
		return $this->getData('artist');
	}
	public function setSongLanguage($lang){
		$this->setData($lang, 'songsLang');
	}
	public function getSongLanguage(){
		return $this->getData('songsLang');
	}
	public function setAlbum(albumObj $album){
		$this->setData($album, 'album');
		$this->setSongs($album->songs);
	}
	public function getAlbum(){
		return $this->getData('album');
	}
	public function setSongs($songs){
		$this->setData($songs, 'songs');
	}
	public function getSongs(){
		return $this->getData('songs');
	}
	public function setAlbums(array $albums){
		$this->setData($albums, 'albums');
	}
	protected function getAlbums():array{
		return $this->getData('albums');
	}
	public function export(){
		return array(
			'data' => array(
				'artist' => $this->getArtist()->toArray(),
				'album' => $this->getAlbum()->toArray(),
				'songs' => dbObject::objectToArray($this->getSongs()),
				'language' => $this->getSongLanguage()
			)
		);
	}
}
