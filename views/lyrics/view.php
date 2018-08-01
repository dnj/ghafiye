<?php
namespace packages\ghafiye\views\lyrics;
use packages\ghafiye\song;
use packages\ghafiye\group;
use packages\ghafiye\person;

class view extends \packages\ghafiye\views\form {
	public function setSinger(person $singer){
		$this->setData($singer, 'singer');
	}
	public function setSong(song $song){
		$this->setData($song, 'song');
	}
	public function getSong(){
		return $this->getData('song');
	}
	public function setLyrices($lyrics){
		$this->setData($lyrics, 'lyrics');
	}
	public function getLyrices(){
		return $this->getData('lyrics');
	}
	public function setLyricsLanguage($lang){
		$this->setData($lang, 'lyricsLang');
	}
	public function getLyricsLanguage(){
		return $this->getData('lyricsLang');
	}
	public function setlikeStatus($status){
		$this->setData($status, "liked");
	}
	protected function getlikeStatus(){
		return $this->getData("liked");
	}
	public function setGroup(group $group){
		$this->setData($group, 'group');
	}
	protected function getGroup():group{
		return $this->getData('group');
	}
}
