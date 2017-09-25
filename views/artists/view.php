<?php
namespace packages\ghafiye\views\artists;
use \packages\ghafiye\group;
use \packages\ghafiye\person;
use \packages\base\db\dbObject;
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
	public function setGroup(group $group){
		$this->setData($group, 'group');
	}
	protected function getGroup():group{
		return $this->getData('group');
	}
	public function export(){
		$artist = $this->getArtist() ? $this->getArtist() : $this->getGroup();
		return array(
			'data' => array(
				'artist' => $artist->toArray(),
				'songs' => dbObject::objectToArray($this->getSongs()),
				'albums' => dbObject::objectToArray($this->getAlbums()),
				'language' => $this->getSongLanguage()
			)
		);
	}
}
