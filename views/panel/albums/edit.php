<?php
namespace packages\ghafiye\views\panel\album;
use \packages\ghafiye\album;
use \packages\ghafiye\views\form;
use \packages\ghafiye\authorization;
class edit extends form{
	public function setAlbum(album $album){
		$this->setData($album, "album");
		$this->setDataForm($album->avatar, "avatar");
		$this->setDataForm($album->lang, "album-lang");
	}
	protected function getAlbum(){
		return $this->getData("album");
	}
}
