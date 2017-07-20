<?php
namespace themes\clipone\views\ghafiye\crawler;
use \packages\base\translator;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\formTrait;
use \packages\ghafiye\crawler\queue;
use \packages\ghafiye\views\panel\crawler\add as crawlerAdd;
class add extends crawlerAdd{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle([
			translator::trans('ghafiye.panle.crawler'),
			translator::trans('ghafiye.panle.crawler.add')
		]);
		$this->addBodyClass('crawler-add');
		navigation::active("crawler");
		if(!$this->getDataForm('type')){
			$this->setDataForm(queue::artist, 'type');
		}
	}
	protected function getLnagsForSelect(){
		$langs = [];
		foreach(['en', 'fa'] as $lang){
			$langs[] = [
				'title' => translator::trans("translations.langs.{$lang}"),
				'value' => $lang
			];
		}
		foreach(translator::$allowlangs as $lang){
			if(in_array($lang, ['en', 'fa'])){
				continue;
			}
			$langs[] = [
				'title' => translator::trans("translations.langs.{$lang}"),
				'value' => $lang
			];
		}
		return $langs;
	}
	protected function getTypesForSelect():array{
		$types = [];
		foreach(queue::types as $type => $value){
			if(in_array($value, [queue::genre, queue::album])){
				continue;
			}
			$types[] = [
				'icon' => $this->getTypeIcon($value),
				'link' => '#',
				'class' => ['changeQueueType'],
				'data' => [
					'value' => $value,
				],
				'title' => translator::trans($this->getTypeTranslate($value))
			];
		}
		return $types;
	}
	private function getTypeIcon(int $type):string{
		$icon = '';
		switch($type){
			case(queue::artist):
				$icon = 'fa fa-user';
				break;
			case(queue::track):
				$icon = 'fa fa-music';
				break;
		}
		return $icon;
	}
	private function getTypeTranslate(int $type):string{
		$text = '';
		switch($type){
			case(queue::artist):
				$text = 'ghafiye.panel.crawler.queue.type.artist';
				break;
			case(queue::track):
				$text = 'ghafiye.panel.crawler.queue.type.track';
				break;
		}
		return $text;
	}
	protected function getTypeInputGroup():array{
		$type = $this->getDataForm('type');
		return [
			'left' => [
				[
					'type' => 'button',
					'class' => ['btn', 'btn-default', 'queueType'],
					'icon' => $this->getTypeIcon($type),
					'text' => translator::trans($this->getTypeTranslate($type)),
					'dropdown' => $this->getTypesForSelect()
				]
			]
		];
	}
}
