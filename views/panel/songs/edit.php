<?php
namespace packages\ghafiye\views\panel\song;
use \packages\base\{http, db\dbObject, views\traits\form as formTrait};
use \packages\ghafiye\{song, views\form, views\listview, song\person, song\lyric};
use \packages\userpanel\date;
class edit extends listview{
	use formTrait;
	public function setSong(song $song){
		$this->setData($song, 'song');
		$this->setDataForm($song->toArray());
		if($song->album){
			$this->setDataForm($song->album->getTitle(), 'album_name');
		}
		if($song->group){
			$this->setDataForm($song->group->getTitle(), 'group_name');
		}
		$titles = array();
		foreach($song->titles as $title){
			$titles[$title->lang] = $title->title;
		}
		$this->setDataForm($titles, "titles");
		$this->setDataForm($song->getLyricByLang()[0]->lang, 'lyric_lang');
		$lyrics = [];
		foreach($song->getLyricByLang() as $lyric){
			$lyrics[] = array(
				'time' => $this->formatTime($lyric->time),
				'text' => $lyric->text,
				'id' => $lyric->id
			);
		}
		$this->setDataForm($lyrics, 'lyric');
		$persons = [];
		foreach($song->persons as $person){
			$persons[$person->data['person']] = array(
				"id" => $person->data['person'],
				"primary" => $person->primary,
				"role" => $person->role
			);
		}
		$this->setDataForm($persons, "persons");
		$this->setDataForm(date::format("Y/m/d H:i:s", $song->release_at), "release_at");
		if ($song->update_at) {
			$this->setDataForm("", "update_at");
		}
	}
	private function formatTime($time){
		$min = floor($time / 60);
		$sec = $time % 60;
		return sprintf('%02d:%02d', $min, $sec);
	}
	protected function getSong(){
		return $this->getData("song");
	}
	public function setAllowLangs($allowlangs){
		$this->setData($allowlangs, 'allowlangs');
	}
	protected function getAllowLangs(){
		return $this->getData("allowlangs");
	}
	public function setGenres($genre){
		$this->setData($genre, 'genre');
	}
	protected function getGenres(){
		return $this->getData("genre");
	}
	public function export(){
		$export = parent::export();
		$song = $this->getSong();
		$item = $song->toArray();
		$item['title'] = $song->title();
		$item['singer'] = $song->getPerson(person::singer)->toArray();
		$item['singer']['name'] = $song->getPerson(person::singer)->name();
		$lang = http::getData("langLyric") ? http::getData("langLyric") : $song->lang;
		$item['orginalLyric'] = array();
		foreach($song->getLyricByLang() as $lyric){
			$item['orginalLyric'][] = array(
				'id' => $lyric->id,
				'time' => $this->formatTime($lyric->time),
				'text' => $lyric->text,
			);
		}
		$item['orginalLang'] = ($lang == $song->lang);
		foreach($song->getLyricByLang($lang) as $lyric){
			$lyricItem = array(
				'text' => $lyric->text,
				'id' => $lyric->id,
				'time' => $this->formatTime($lyric->time),
				'parent' => $lyric->parent ? $lyric->getParent()->id : ''
			);
			$item['lyrics'][] = $lyricItem;
		}
		$export['data']['song'] = $item;
		return $export;
	}
}
