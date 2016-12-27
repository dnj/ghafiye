<?php
namespace packages\ghafiye\views\panel\album;
use \packages\ghafiye\album;
use \packages\ghafiye\views\form;
class delete extends form{
	public function setAlbum(album $album){
		$this->setData($album, "album");
	}
	protected function getAlbum(){
		return $this->getData("album");
	}
}
