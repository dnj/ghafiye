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
		$this->addBodyClass('article');
		$this->addBodyClass('list');
		$this->addBodyClass('blog');
	}
}
