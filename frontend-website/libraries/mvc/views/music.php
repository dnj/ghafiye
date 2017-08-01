<?php
namespace themes\musixmatch\views;
use \packages\ghafiye\genre;
trait musicTrait{
	function getGenres($num = null){
		return genre::getActives($num);
	}
}
