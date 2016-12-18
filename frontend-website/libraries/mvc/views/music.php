<?php
namespace themes\musixmatch\views;
use \packages\base\packages;
use \packages\base\frontend\theme;
use \packages\ghafiye\song;
use \packages\ghafiye\genre;

trait musicTrait{
	protected function songImage(song $song){
		if($song->image){
			return packages::package('ghafiye')->url($song->image);
		}
		return theme::url('assets/images/song.jpg');
	}
	function getGenres($num = null){
		return genre::getActives($num);
	}
}
