<?php
namespace packages\ghafiye\views\artists;
use \packages\base\db\dbObject;
use \packages\ghafiye\person;
class view extends \packages\ghafiye\view{
	public function setArtist(person $artist){
		$this->setData($artist, 'artist');
	}
	public function getArtist(){
		return $this->getData('artist');
	}
	public function setSongs($songs){
		$this->setData($songs, 'songs');
	}
	public function getSongs(){
		return $this->getData('songs');
	}
	public function setSongLanguage($lang){
		$this->setData($lang, 'songsLang');
	}
	public function getSongLanguage(){
		return $this->getData('songsLang');
	}
	public function setAlbums($albums){
		$this->setData($albums, 'albums');
	}
	public function getAlbums(){
		return $this->getData('albums');
	}
	public function export(){
		return array(
			'data' => array(
				'artist' => $this->getArtist()->toArray(),
				'songs' => dbObject::objectToArray($this->getSongs()),
				'albums' => dbObject::objectToArray($this->getAlbums()),
				'language' => $this->getSongLanguage()
			)
		);
	}
}
