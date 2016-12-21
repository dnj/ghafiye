<?php
namespace themes\musixmatch\views\blog;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\blogTrait;

use \packages\blog\views\post\index as blogList;
class listview extends blogList{
	use viewTrait, blogTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans("blog.list.title"));
		$this->addAssets();
	}
	private function addAssets(){
		$this->addCSSFile(theme::url("assets/css/blog.css"));
		$this->addJSFile(theme::url("assets/js/pages/blog.js"));
	}
}
