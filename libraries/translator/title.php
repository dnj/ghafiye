<?php
namespace packages\ghafiye\translator;
use \packages\base\translator;
trait title{
	public function title($lang = null){
		if(!isset($this->relations['titles'])){
			throw new titleRelationException();
		}
		$defaultlang =  translator::getShortCodeLang();
		$originalang = $lang;
		if($lang === null){
			$lang = $defaultlang;
		}
		if(isset($this->data['titles'])){
			foreach($this->data['titles'] as $title){
				if($title->lang == $lang){
					return $title->title;
				}
			}
			if($originalang == null){
				foreach($this->data['titles'] as $title){
					if($title->lang == $defaultlang){
						return $title->title;
					}
				}
			}
		}
		$title = new $this->relations['titles'][1]();
		$title->where($this->relations['titles'][2], $this->id);
		$title->where("lang", $lang);
		if($title->getOne()){
			$this->data['titles'][] = $title;
			return $title->title;
		}
		if($originalang == null){
			$title = new $this->relations['titles'][1]();
			$title->where($this->relations['titles'][2], $this->id);
			$title->where("lang", $defaultlang);
			if($title->getOne()){
				$this->data['titles'][] = $title;
				return $title->title;
			}
		}
		$title = new $this->relations['titles'][1]();
		$title->where($this->relations['titles'][2], $this->id);
		$title->where("lang", 'en');
		if($title->getOne()){
			$this->data['titles'][] = $title;
			return $title->title;
		}
		return false;
	}
	public function encodedTitle($lang = null){
		return strtolower(str_replace(array(' '), '-',$this->title($lang)));
	}
	public static function decodeTitle($title){
		return str_replace('-', ' ',$title);
	}
}
class titleRelationException extends \Exception{}
