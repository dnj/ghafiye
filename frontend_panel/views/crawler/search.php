<?php
namespace themes\clipone\views\ghafiye\crawler;
use \packages\base\translator;
use \packages\userpanel;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;
use \packages\ghafiye\crawler\queue;
use \packages\base\view\error;
use \packages\ghafiye\views\panel\crawler\search as crawlerSearch;
class search extends crawlerSearch{
	use viewTrait, listTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('ghafiye.panle.crawler'));
		$this->setButtons();
		$this->addBodyClass('crawler-search');
		navigation::active("crawler");
		if(empty($this->getCrawlerLists())){
			$this->addNotFoundError();
		}
	}
	private function addNotFoundError(){
		$error = new error();
		$error->setType(error::NOTICE);
		$error->setCode('ghafiye.crawler.queue.notfound');
		if($this->canAdd){
			$error->setData([
				[
					'type' => 'btn-success',
					'txt' => translator::trans('add'),
					'link' => userpanel\url('crawler/queue/add')
				]
			], 'btns');
		}
		$this->addError($error);
	}
	public function setButtons(){
		$this->setButton('delete', $this->canDel, [
			'title' => translator::trans('delete'),
			'icon' => 'fa fa-times',
			'classes' => ['btn', 'btn-xs', 'btn-bricky']
		]);
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			$crawlers = new menuItem("crawler");
			$crawlers->setTitle(translator::trans("ghafiye.panle.crawler"));
			$crawlers->setURL(userpanel\url('crawler/queue'));
			$crawlers->setIcon('fa fa-bug');
			navigation::addItem($crawlers);
		}
	}
	protected function getTypesForSelect():array{
		$types = [
			[
				'title' => translator::trans("choose"),
				'value' => ''
			]
		];
		foreach(queue::types as $type => $value){
			$types[] = [
				'title' => translator::trans("ghafiye.panel.crawler.queue.type.{$type}"),
				'value' => $value
			];
		}
		return $types;
	}
	protected function getTypeTranslate(int $type):string{
		switch($type){
			case(queue::artist):
				return translator::trans('ghafiye.panel.crawler.queue.type.artist');
			case(queue::album):
				return translator::trans('ghafiye.panel.crawler.queue.type.album');
			case(queue::track):
				return translator::trans('ghafiye.panel.crawler.queue.type.track');
		}
	}
	protected function getStatusesForSelect():array{
		$statuses = [
			[
				'title' => translator::trans("choose"),
				'value' => ''
			]
		];
		foreach(queue::statuses as $status => $value){
			$statuses[] = [
				'title' => translator::trans("ghafiye.panel.crawler.queue.status.{$status}"),
				'value' => $value
			];
		}
		return $statuses;
	}
	public function getComparisonsForSelect(){
		return [
			[
				'title' => translator::trans('search.comparison.contains'),
				'value' => 'contains'
			],
			[
				'title' => translator::trans('search.comparison.equals'),
				'value' => 'equals'
			],
			[
				'title' => translator::trans('search.comparison.startswith'),
				'value' => 'startswith'
			]
		];
	}
}
