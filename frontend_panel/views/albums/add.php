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
use \packages\ghafiye\views\panel\album\add as albumADD;

class add extends albumADD{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('albums'),
			translator::trans('add')
		));
		$this->addAssests();
		$this->setNavigation();
	}
	private function addAssests(){
		$this->addCSSFile(theme::url("assets/css/album.image.css"));
		$this->addJSFile(theme::url("assets/js/pages/album.add.js"));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css'));
		$this->addJSFile(theme::url("assets/plugins/jquery.growl/javascripts/jquery.growl.js"));
		$this->addCSSFile(theme::url("assets/plugins/jquery.growl/stylesheets/jquery.growl.css"));
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('albums'));
		$item = new menuItem("add");
		$item->setTitle(translator::trans('add'));
		$item->setIcon('fa fa-plus');
		breadcrumb::addItem($item);
		navigation::active("albums");
	}
	protected function getImage(){
		return packages::package('ghafiye')->url(options::get('packages.ghafiye.albums.deafault_image'));
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
