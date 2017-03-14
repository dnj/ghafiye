<?php
namespace themes\clipone\views\ghafiye\song;
use \packages\base\options;
use \packages\base\packages;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\formTrait;

use \packages\ghafiye\song;
use \packages\ghafiye\album;
use \packages\ghafiye\group;
use \packages\ghafiye\song\person;
use \packages\ghafiye\views\panel\song\edit as EditSongs;

class edit extends EditSongs{
	use viewTrait, formTrait;
	protected $song;
	function __beforeLoad(){
		$this->song = $this->getSong();
		$this->setTitle(translator::trans('ghafiye.editSong'));
		$this->addBodyClass("songs");
		$this->setNavigation();
		$this->addAssets();
		$this->handlerErrors();
		$this->formData();
	}
	private function setNavigation(){
		navigation::active("songs");
	}
	private function addAssets(){
		$this->addCSSFile(theme::url("assets/css/songs.css"));
		$this->addJSFile(theme::url("assets/js/pages/song.edit.js"));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css'));
		$this->addJSFile(theme::url('assets/plugins/x-editable/js/bootstrap-editable.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/x-editable/css/bootstrap-editable.css'));
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
	public function formData(){
		if($album = $this->getDataForm("album")){
			$albumName = album::byId($album);
			if($albumName){
				$this->setDataForm($albumName->title($this->getDataForm("lang")), "album_name");
			}
		}
		if($group = $this->getDataForm("group")){
			$groupName = group::byId($group);
			if($groupName){
				$this->setDataForm($groupName->title($this->getDataForm("lang")), "group_name");
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
		return packages::package("ghafiye")->url($this->song->image ? $this->song->image : options::get("packages.ghafiye.persons.deafault_image"));
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
				'value' => person::singer
			),
			array(
				'title' => translator::trans("ghafiye.panel.song.person.role.writer"),
				'value' => person::writer
			),
			array(
				'title' => translator::trans("ghafiye.panel.song.person.role.composer"),
				'value' => person::composer
			)
		);
	}
}
