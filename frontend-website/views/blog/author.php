<?php
namespace themes\musixmatch\views\blog;
use \packages\base\translator;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\blogTrait;
use \themes\musixmatch\views\formTrait;
use \themes\musixmatch\views\listTrait;
use \packages\blog\views\post\author as blogAuthor;
class author extends blogAuthor{
	use viewTrait, blogTrait, formTrait, listTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans("blog.list.title"),
			translator::trans("blog.list.title.author", array("author" => $this->getPosts()[0]->author->getFullName()))
		));
		$this->addBodyClass('article');
		$this->addBodyClass('list');
		$this->addBodyClass('blog');
	}
}
