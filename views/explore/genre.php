<?php
namespace packages\ghafiye\views\explore;
use \packages\base\db\dbObject;
use \packages\ghafiye\views\listview;
use \packages\ghafiye\genre as genreObj;
class genre extends listview{
	public function setSongs($songs){
		$this->setDataList($songs);
	}
	public function getSongs(){
		return $this->getDataList();
	}
	public function setGenre(genreObj $genre){
		$this->setData($genre, 'genre');
	}
	public function getGenre(){
		return $this->getData('genre');
	}
	public function setSongLanguage($lang){
		$this->setData($lang, 'songsLang');
	}
	public function getSongLanguage(){
		return $this->getData('songsLang');
	}
	public function export(){
		$export = parent::export();
		$export['data']['genre'] = $this->getGenre()->toArray();
		$export['data']['lang'] = $this->getSongLanguage();
		return $export;
	}
}
