<?php
namespace themes\clipone\views\ghafiye\song;
use \packages\base\{options, packages, translator, frontend\theme};
use \themes\clipone\{viewTrait, navigation, views\formTrait, views\listTrait};
use \packages\ghafiye\{song, person, song\person as songPerson, views\panel\song\add as ADDSongs};
use \packages\userpanel\date;

class add extends ADDSongs{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('ghafiye.addSong'));
		$this->addBodyClass("songs");
		$this->addBodyClass("song_add");
		$this->setNavigation();
		$this->handlerErrors();
		$this->handlerFormData();
	}
	private function setNavigation(){
		navigation::active("songs");
	}
	public function handlerErrors(){
		foreach($this->getFormErrors() as $error){
			if($error->input == 'album'){
				$error->setInput("album_name");
			}elseif($error->input == 'group'){
				$error->setInput("group_name");
			}elseif(preg_match("/lyric\[(\d+)\]\[(?:id|parent)\]/i", $error->input, $matches)){
				$error->setInput("lyric[{$matches[1]}][text]");
			}
		}
	}
	public function handlerFormData(){
		$lyrics = $this->getDataForm("lyric");
		if(!$lyrics){
			for($i=0;$i<3;$i++){
				$this->setDataForm("00:0{$i}","lyric[{$i}][time]");
			}
		}else{
			foreach($lyrics as $key => $lyric){
				if(isset($lyric['time'], $lyric['text'])){
					$this->setDataForm($lyric['time'],"lyric[{$key}][time]");
					$this->setDataForm($lyric['text'],"lyric[{$key}][text]");
				}
			}
		}
		if($person = $this->getDataForm("person")){
			$personName = person::byId($person);
			if($personName){
				if($personName){
					$this->setDataForm($personName->name($this->getDataForm("lang")), "person_name");
				}
			}
		}
		if (!$this->getDataForm("release_at")) {
			$this->setDataForm(date::format("Y/m/d H:i:s", date::time()), "release_at");	
		}
	}
	protected function getGenreForSelect(){
		$genres = array();
		foreach($this->getGenres() as $genre){
			$genres[] = array(
				'title' => $genre->title('fa'),
				'value' => $genre->id
			);
		}
		return $genres;
	}
	protected function getLangForSelect(){
		$langs = [];
		foreach(['en', 'fa'] as $lang){
			$langs[] = array(
				'title' => translator::trans("translations.langs.{$lang}"),
				'value' => $lang
			);
		}
		foreach(translator::$allowlangs as $lang){
			if(in_array($lang, ['en', 'fa'])){
				continue;
			}
			$langs[] = array(
				'title' => translator::trans("translations.langs.{$lang}"),
				'value' => $lang
			);
		}
		return $langs;
	}
	protected function getSongImage(){
		if($image = $this->getDataForm("image")){
			return $image;
		}
		return packages::package("ghafiye")->url('storage/public/default-image.png');
	}
	protected function getStatusForSelect(){
		return array(
			array(
				'title' => translator::trans("ghafiye.panel.song.status.publish"),
				'value' => song::publish
			),
			array(
				'title' => translator::trans("ghafiye.panel.song.status.draft"),
				'value' => song::draft
			)
		);
	}
	protected function getRolesForSelect(){
		return array(
			array(
				'title' => translator::trans("ghafiye.panel.song.person.role.singer"),
				'value' => songPerson::singer
			),
			array(
				'title' => translator::trans("ghafiye.panel.song.person.role.writer"),
				'value' => songPerson::writer
			),
			array(
				'title' => translator::trans("ghafiye.panel.song.person.role.composer"),
				'value' => songPerson::composer
			)
		);
	}
}
