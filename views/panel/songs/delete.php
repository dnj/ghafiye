<?php
namespace packages\ghafiye\views\panel\song;
use \packages\ghafiye\song;
use \packages\ghafiye\views\form;
class delete extends form{
	public function setSong(song $song){
		$this->setData($song, 'song');
	}
	protected function getSong(){
		return $this->getData("song");
	}
}
