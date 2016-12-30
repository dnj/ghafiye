<?php
namespace packages\ghafiye\controllers\panel;
use \packages\base;
use \packages\base\IO;
use \packages\base\db;
use \packages\base\http;
use \packages\base\NotFound;
use \packages\base\packages;
use \packages\base\view\error;
use \packages\base\translator;
use \packages\base\db\parenthesis;
use \packages\base\inputValidation;
use \packages\base\views\FormError;

use \packages\userpanel;
use \packages\userpanel\controller;

use \packages\ghafiye\view;
use \packages\ghafiye\person;
use \packages\ghafiye\authorization;

class persons extends controller{
	protected $authentication = true;
	public function listview(){
		authorization::haveOrFail('persons_list');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\person\\listview");
		$person = new person();
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'name_prefix' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'first_name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'middle_name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'last_name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'name_suffix' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'word' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'comparison' => array(
				'values' => array('equals', 'startswith', 'contains'),
				'default' => 'contains',
				'optional' => true
			)
		);
		$this->response->setStatus(true);
		try{
			$inputs = $this->checkinputs($inputsRules);
			foreach(array('id', 'name_prefix', 'first_name', 'middle_name', 'last_name', 'name_suffix') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id'))){
						$comparison = 'equals';
					}
					$person->where($item, $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				foreach(array("name_prefix", "first_name", "middle_name", "last_name", "name_suffix") as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where($item, $inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				$parenthesis->where("ghafiye_persons_names.name", $inputs['word'], $inputs['comparison'], "or");
				$person->where($parenthesis);
				db::join("ghafiye_persons_names", "ghafiye_persons_names.person=ghafiye_persons.id", "INNER");
				db::setQueryOption("DISTINCT");
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$person->pageLimit = $this->items_per_page;
		$persons = $person->paginate($this->page, array("ghafiye_persons.*"));
		$this->total_pages = $person->totalPages;
		$view->setDataList($persons);
		$view->setPaginate($this->page, $person->totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	private function getPersonById($id){
		$person = person::byId($id);
		if(!$person){
			throw new NotFound;
		}
		return $person;
	}
	private function evaluationImage($image){
		$type = getimagesize($image['tmp_name']);
		if(in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
			$name = IO\md5($image['tmp_name']);
			switch($type[2]){
				case(IMAGETYPE_JPEG):
					$type_name = '.jpg';
					break;
				case(IMAGETYPE_GIF):
					$type_name = '.gif';
					break;
				case(IMAGETYPE_PNG):
					$type_name = '.png';
					break;
			}
			$directory = packages::package('ghafiye')->getFilePath("storage/public/persons/".$name.$type_name);
			if(move_uploaded_file($image['tmp_name'], $directory)){
				$return = "storage/public/persons/".$name.$type_name;
				return $return;
			}else{
				throw new inputValidation($image);
			}
		}else{
			throw new inputValidation($image);
		}
	}
	public function edit($data){
		authorization::haveOrFail('person_edit');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\person\\edit");
		$person = $this->getPersonById($data['id']);
		$view->setPerson($person);
		$inputsRules = array(
			'name_prefix' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'first_name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'middle_name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'last_name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'name_suffix' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'gender' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'avatar' => array(
				'type' => 'file',
				'optional' => true,
				'empty' => true
			),
			'cover' => array(
				'type' => 'file',
				'optional' => true,
				'empty' => true
			),
			'names' => array()
		);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$inputs = $this->checkinputs($inputsRules);
				if(is_array($inputs['names'])){
					foreach($inputs['names'] as $key => $name){
						if(!in_array($key, translator::$allowlangs) or !$name){
							throw new inputValidation("names[{$key}]");
						}
					}
				}else{
					throw new inputValidation("names");
				}
				$person_full_name = array('name_prefix','first_name','middle_name','last_name','name_suffix');
				$hasName = false;
				foreach($person_full_name as $item){
					if(array_key_exists($item, $inputs) and $inputs[$item]){
						$hasName = true;
						break;
					}
				}
				if(!$hasName){
					throw new InvalidPersoName();
				}
				foreach($person->names as $name){
					if(isset($inputs['names'][$name->lang])){
						if($inputs['names'][$name->lang] != $name->name){
							$name->name = $inputs['names'][$name->lang];
							$name->save();
						}
						unset($inputs['names'][$name->lang]);
					}else{
						$name->delete();
					}
				}
				foreach($inputs['names'] as $lang => $name){
					if($name){
						$person->addName($name, $lang);
					}else{
						throw new inputValidation("names[{$lang}]");
					}
				}

				foreach(array('name_prefix', 'first_name', 'middle_name', 'last_name', 'name_suffix', 'gender') as $item){
					if(array_key_exists($item, $inputs) and $inputs[$item]){
						$person->$item = $inputs[$item];
					}
				}
				foreach(array('avatar', 'cover') as $item){
					if(array_key_exists($item, $inputs) and $inputs[$item]){
						if($inputs[$item]['error'] == 0){
							$person->$item = $this->evaluationImage($inputs[$item]);
						}elseif($inputs[$item]['error'] != 4){
							throw new inputValidation($item);
						}
					}
				}
				$person->save();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("persons/edit/".$person->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(InvalidPersoName $e){
				$error = new error();
				$error->setCode('person_name.empty');
				$view->addError($error);
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function add(){
		authorization::haveOrFail('person_add');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\person\\add");
		$inputsRules = array(
			'name_prefix' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'first_name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'middle_name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'last_name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'name_suffix' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'gender' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'avatar' => array(
				'type' => 'file',
				'optional' => true,
				'empty' => true
			),
			'cover' => array(
				'type' => 'file',
				'optional' => true,
				'empty' => true
			),
			'name' => array(
				'type' => 'string'
			),
			'lang' => array(
				'type' => 'string'
			)
		);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$inputs = $this->checkinputs($inputsRules);
				$person_full_name = array('name_prefix','first_name','middle_name','last_name','name_suffix');
				$hasName = false;
				foreach($person_full_name as $item){
					if(array_key_exists($item, $inputs) and $inputs[$item]){
						$hasName = true;
						break;
					}
				}
				if(!$hasName){
					throw new InvalidPersoName();
				}
				if(!in_array($inputs['lang'], translator::$allowlangs))
					throw new inputValidation("lang");
				$person = new person();
				foreach(array('name_prefix', 'first_name', 'middle_name', 'last_name', 'name_suffix', 'gender') as $item){
					if(array_key_exists($item, $inputs) and $inputs[$item]){
						$person->$item = $inputs[$item];
					}
				}
				foreach(array('avatar', 'cover') as $item){
					if($inputs[$item]['error'] == 0){
						$person->$item = $this->evaluationImage($inputs[$item]);
					}elseif($inputs[$item]['error'] != 4){
						throw new inputValidation($item);
					}
				}
				$person->save();
				$person->addName($inputs['name'], $inputs['lang']);
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("persons/edit/".$person->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(InvalidPersoName $e){
				$error = new error();
				$error->setCode('person_name.empty');
				$view->addError($error);
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('person_delete');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\person\\delete");
		$person = $this->getPersonById($data['id']);
		$view->setPerson($person);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$person->delete();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("persons"));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
}
class InvalidPersoName extends \Exception{}
