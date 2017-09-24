<?php
namespace packages\ghafiye\views\artists;
use \packages\base\db\dbObject;
use \packages\ghafiye\{person, group, view, album as albumObj};
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
				'album' => $this->getAlbum()->toArray(),
				'songs' => dbObject::objectToArray($this->getSongs()),
				'language' => $this->getSongLanguage()
			)
		);
	}
}
