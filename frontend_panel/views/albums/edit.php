<?php
namespace themes\clipone\views\ghafiye\album;
use \packages\base\options;
use \packages\base\packages;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\album;
use \packages\ghafiye\views\panel\album\edit as albumEdit;

class edit extends albumEdit{
	use viewTrait, formTrait;
	protected $album;
	function __beforeLoad(){
		$this->album = $this->getAlbum();
		$this->setTitle(array(
			translator::trans('albums'),
			$this->album->getTitle(),
			translator::trans('edit')
		));
		$this->addBodyClass('album_edit');
		$this->addAssests();
		$this->setNavigation();
	}
	private function addAssests(){
		$this->addJSFile(theme::url("assets/js/pages/album.edit.js"));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css'));
		$this->addJSFile(theme::url('assets/plugins/x-editable/js/bootstrap-editable.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/x-editable/css/bootstrap-editable.css'));
		$this->addJSFile(theme::url("assets/plugins/jquery.growl/javascripts/jquery.growl.js"));
		$this->addCSSFile(theme::url("assets/plugins/jquery.growl/stylesheets/jquery.growl.css"));
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('albums'));
		$item = new menuItem("edit");
		$item->setTitle(translator::trans('edit'));
		$item->setIcon('fa fa-edit');
		breadcrumb::addItem($item);
		navigation::active("albums");
	}
	protected function getImage($image){
		return packages::package('ghafiye')->url($image ? $image : options::get('packages.ghafiye.albums.deafault_image'));
	}
	protected function getLangsForSelect(){
		$langs = array();
		foreach(translator::$allowlangs as $lang){
			$langs[] = array(
				'title' => translator::trans("translations.langs.{$lang}"),
				'value' => $lang
			);
		}
		return $langs;
	}
}
