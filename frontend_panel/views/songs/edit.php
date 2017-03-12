<?php
namespace themes\clipone\views\ghafiye\song;
use \packages\base\packages;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\formTrait;
use \themes\clipone\views\listTrait;

use \packages\ghafiye\song;
use \packages\ghafiye\views\panel\song\edit as EditSongs;

class edit extends EditSongs{
	use viewTrait, listTrait, formTrait;
	protected $song;
	function __beforeLoad(){
		$this->song = $this->getSong();
		$this->setTitle(translator::trans('ghafiye.editSong'));
		$this->addBodyClass("songs");
		$this->setNavigation();
		$this->addAssets();
		$this->setButtons();
		$this->handlerErrors();
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
		$this->addJSFile(theme::url('assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css'));
	}
	public function handlerErrors(){
		foreach($this->getFormErrors() as $error){
			if(preg_match("/lyric\[(\d+)\]\[(?:id|parent)\]/i", $error->input, $matches)){
				$error->setInput("lyric[{$matches[1]}][text]");
			}
		}
	}
	public function setButtons(){
		$this->canNameDel = true;
		$this->setButton('delete', true, array(
			'title' => translator::trans('delete'),
			'icon' => 'fa fa-times',
			'classes' => array('btn', 'btn-xs', 'btn-bricky', 'lang-del')
		));
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
		return packages::package("ghafiye")->url($this->song->image);
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
}
