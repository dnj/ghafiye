<?php
namespace themes\musixmatch\views;
use \packages\base\packages;
use \packages\base\frontend\theme;
use \packages\ghafiye\song;
use \packages\ghafiye\genre;

trait musicTrait{
	protected function songImage(song $song){
		return packages::package('ghafiye')->url($song->image ? $song->image : "storage/public/songs/default-image.png");
	}
	function getGenres($num = null){
		return genre::getActives($num);
	}
}
