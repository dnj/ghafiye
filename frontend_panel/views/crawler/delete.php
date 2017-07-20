<?php
namespace themes\clipone\views\ghafiye\crawler;
use \packages\base\translator;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\formTrait;
use \packages\ghafiye\views\panel\crawler\delete as crawlerDelete;
class delete extends crawlerDelete{
	use viewTrait;
	protected $queue;
	function __beforeLoad(){
		$this->queue = $this->getQueue();
		$this->setTitle(array(
			translator::trans('ghafiye.panle.crawler'),
			translator::trans('ghafiye.panle.crawler.delete')
		));
		navigation::active("crawlers");
	}
}
