<?php
namespace themes\musixmatch\views\pages;
use \packages\base\translator;
use \themes\musixmatch\viewTrait;
use \packages\pages\views\page\view as pageView;
class view extends pageView{
	use viewTrait;
	protected $page;
	function __beforeLoad(){
		$this->page = $this->getPage();
		$this->setTitle($this->page->title);
		$this->addBodyClass('pages');
		$this->addBodyClass('article');
	}
}
