<?php
namespace themes\musixmatch\views\blog;
use \packages\base\view\error;
use \packages\base\translator;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\blogTrait;
use \themes\musixmatch\views\formTrait;
use \themes\musixmatch\views\listTrait;
use \packages\blog\views\post\index as blogList;
class listview extends blogList{
	use viewTrait, blogTrait, formTrait, listTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans("blog.list.title"));
		$this->addBodyClass('article');
		$this->addBodyClass('list');
		$this->addBodyClass('blog');
	}
}
