<?php
namespace themes\musixmatch\views\blog;
use \packages\base\translator;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\blogTrait;
use \themes\musixmatch\views\formTrait;
use \themes\musixmatch\views\listTrait;
use \packages\blog\views\post\search as postSearch;
class search extends postSearch{
	use viewTrait, blogTrait, formTrait, listTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans("blog.search.title", ['word'=>$this->getWord()]),
		));
		$this->addBodyClass('article');
		$this->addBodyClass('blog');
		$this->addBodyClass('search');
	}
}
