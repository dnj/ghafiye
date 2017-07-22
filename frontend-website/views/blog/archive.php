<?php
namespace themes\musixmatch\views\blog;
use \packages\base\translator;
use \packages\userpanel\date;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\blogTrait;
use \themes\musixmatch\views\formTrait;
use \packages\blog\views\post\archive as postArchive;
class archive extends postArchive{
	use viewTrait, blogTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans("blog.list.title"),
			translator::trans("blog.list.title.archive", array("date" => date::format("F Y", $this->getPosts()[0]->date)))
		));
		$this->addBodyClass('article');
		$this->addBodyClass('list');
		$this->addBodyClass('blog');
	}
}
