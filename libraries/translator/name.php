<?php
namespace packages\ghafiye\translator;
use \packages\base\db;
use \packages\base\translator;
trait name{
	public function name($lang = null){
		if(!isset($this->relations['names'])){
			throw new nameRelationException();
		}
		$defaultlang =  translator::getShortCodeLang();
		$originalang = $lang;
		if($lang === null){
			$lang = $defaultlang;
		}
		if(isset($this->data['names'])){
			foreach($this->data['names'] as $name){
				if($name->lang == $lang){
					return $name->name;
				}
			}
			if($originalang == null){
				foreach($this->data['names'] as $name){
					if($name->lang == $defaultlang){
						return $name->name;
					}
				}
			}
		}
		$name = new $this->relations['names'][1]();
		$name->where($this->relations['names'][2], $this->id);
		$name->where("lang", $lang);
		if($name->getOne()){
			$this->data['names'][] = $name;
			return $name->name;
		}
		if($originalang == null){
			$name = new $this->relations['names'][1]();
			$name->where($this->relations['names'][2], $this->id);
			$name->where("lang", $defaultlang);
			if($name->getOne()){
				$this->data['names'][] = $name;
				return $name->name;
			}
		}
		$name = new $this->relations['names'][1]();
		$name->where($this->relations['names'][2], $this->id);
		$name->where("lang", 'en');
		if($name->getOne()){
			$this->data['names'][] = $name;
			return $name->name;
		}
		return false;
	}
	public function encodedName($lang = null){
		return strtolower(str_replace(array(' '), '-',$this->name($lang)));
	}
	public static function decodeName($name){
		return str_replace('-', ' ',$name);
	}
	public function addName($nametxt, $lang){
		$name = new $this->relations['names'][1]();
		$name->person = $this->id;
		$name->name = $nametxt;
		$name->lang = $lang;
		$name->save();
	}
}
class nameRelationException extends \Exception{}
