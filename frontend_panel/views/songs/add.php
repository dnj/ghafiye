<?php
namespace themes\clipone\views\ghafiye\song;
use \packages\base\options;
use \packages\base\packages;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\formTrait;
use \themes\clipone\views\listTrait;

use \packages\ghafiye\song;
use \packages\ghafiye\person;
use \packages\ghafiye\song\person as songPerson;
use \packages\ghafiye\views\panel\song\add as ADDSongs;

class add extends ADDSongs{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('ghafiye.addSong'));
		$this->addBodyClass("songs");
		$this->setNavigation();
		$this->addAssets();
		$this->handlerErrors();
		$this->handlerFormData();
	}
	private function setNavigation(){
		navigation::active("songs");
	}
	private function addAssets(){
		$this->addCSSFile(theme::url("assets/css/songs.css"));
		$this->addJSFile(theme::url("assets/js/pages/song.add.js"));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css'));
		$this->addJSFile(theme::url('assets/plugins/x-editable/js/bootstrap-editable.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/x-editable/css/bootstrap-editable.css'));
	}
	public function handlerErrors(){
		if($error->input == 'album'){
			$error->setInput("album_name");
		}elseif($error->input == 'group'){
			$error->setInput("group_name");
		}elseif(preg_match("/lyric\[(\d+)\]\[(?:id|parent)\]/i", $error->input, $matches)){
			$error->setInput("lyric[{$matches[1]}][text]");
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
		$langs = array();
		foreach($this->getAllowLangs() as $lang){
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
		return packages::package("ghafiye")->url(options::get("packages.ghafiye.persons.deafault_image"));
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
