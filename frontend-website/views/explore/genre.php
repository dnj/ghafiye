<?php
namespace themes\musixmatch\views\explore;
use \packages\base;
use \packages\base\translator;
use \packages\ghafiye\views\explore\genre as genreView;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\musicTrait;

class genre extends genreView{
	use viewTrait,musicTrait;
	protected $genre;
	function __beforeLoad(){
		$this->genre = $this->getGenre();
		$this->setTitle(translator::trans('explore.genre.title', array(
			'genre' => $this->genre->title($this->getSongLanguage())
		)));
		$this->addBodyClass('explore');
	}
}
