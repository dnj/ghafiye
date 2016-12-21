<?php
namespace themes\musixmatch\views\blog;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\blogTrait;

use \packages\blog\views\post\category as blogCategories;
class category extends blogCategories{
	use viewTrait, blogTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans("blog.list.title"),
			translator::trans("blog.list.title.category", array("category" => $this->getCategory()->title))
		));
		$this->addAssets();
	}
	private function addAssets(){
		$this->addCSSFile(theme::url("assets/css/blog.css"));
		$this->addJSFile(theme::url("assets/js/pages/blog.js"));
	}
}
