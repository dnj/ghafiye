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
use \packages\base\inputValidation;
use \packages\base\views\FormError;

use \packages\userpanel;
use \packages\userpanel\controller;

use \packages\ghafiye\view;
use \packages\ghafiye\group;
use \packages\ghafiye\person;
use \packages\ghafiye\group\title;
use \packages\ghafiye\authorization;

class groups extends controller{
	protected $authentication = true;
	public function listview(){
		authorization::haveOrFail('groups_list');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\group\\listview");
		$group = new group();
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'lang' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'title' => array(
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
			foreach(array('id', 'lang') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id'))){
						$comparison = 'equals';
					}
					$group->where($item, $inputs[$item], $comparison);
				}
			}
			if(isset($inputs["title"]) and $inputs["title"]){
				$titles = title::where("title", $inputs["title"], $inputs["comparison"])->get();
				foreach($titles as $title){
					$group->where("id", $title->group, 'equals');
				}
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$group->pageLimit = $this->items_per_page;
		$groups = $group->paginate($this->page);
		$this->total_pages = $group->totalPages;
		$view->setDataList($groups);
		$view->setPaginate($this->page, $group->totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('group_edit');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\group\\edit");
		$group = group::byId($data['id']);
		if(!$group){
			throw new NotFound;
		}
		$view->setGroup($group);
		$inputsRules = array(
			'avatar' => array(
				'type' => 'file',
				'optional' => true,
				'empty' => true
			),
			'group-lang' => array(
				'type' => 'string',
				'optional' => true
			),
			'titles' => array(),
			'persons' => array(
				'empty' => true,
				'optional' => true
			)
		);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$inputs = $this->checkinputs($inputsRules);
				if(is_array($inputs['titles'])){
					foreach($inputs['titles'] as $key => $title){
						if(!in_array($key, translator::$allowlangs) or !$title){
							throw new inputValidation("titles[{$key}]");
						}
					}
				}else{
					throw new inputValidation("titles");
				}
				if(!array_key_exists("persons", $inputs) or !is_array($inputs['persons'])){
					$inputs['persons'] = array();
				}
				foreach($inputs['persons'] as $key => $person){
					$person = person::byId($person);
					if(!$person){
						throw new inputValidation("persons[{$key}]");
					}
				}
				if(!isset($inputs['titles'][$inputs['group-lang']])){
					throw new translatedGroupLang();
				}
				foreach($group->titles as $title){
					if(isset($inputs['titles'][$title->lang])){
						if($inputs['titles'][$title->lang] != $title->title){
							$title->title = $inputs['titles'][$title->lang];
							$title->save();
						}
						unset($inputs['titles'][$title->lang]);
					}else{
						$title->delete();
					}
				}
				foreach($inputs['titles'] as $lang => $title){
					if($title){
						$group->addTitle($title, $lang);
					}else{
						throw new inputValidation("titles[{$lang}]");
					}
				}

				foreach($group->persons as $person){
					if(($key = array_search($person->data['person'], $inputs['persons'])) === false){
						$person->delete();
					}else{
						unset($inputs['persons'][$key]);
					}
				}
				foreach($inputs['persons'] as $person){
					$person = new group\person(array(
						'group_id' => $group->id,
						'person' => $person
					));
					$person->save();
				}
				if(isset($inputs['group-lang']) and $inputs['group-lang']){
					if(!in_array($inputs['group-lang'], translator::$allowlangs)){
						throw new inputValidation("group-lang");
					}
					$group->lang = $inputs['group-lang'];
				}
				if($inputs["avatar"]['error'] == 0){
					$type = getimagesize($inputs["avatar"]['tmp_name']);
					if(in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
						$title = IO\md5($inputs["avatar"]['tmp_name']);
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
						$directory = packages::package('ghafiye')->getFilePath("storage/public/groups/".$title.$type_name);
						if(move_uploaded_file($inputs["avatar"]['tmp_name'], $directory)){
							$group->avatar = "storage/public/groups/".$title.$type_name;
						}else{
							throw new inputValidation($inputs["avatar"]);
						}
					}else{
						throw new inputValidation($inputs["avatar"]);
					}
				}elseif($inputs["avatar"]['error'] != 4){
					throw new inputValidation("avatar");
				}
				$group->save();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("groups/edit/".$group->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(translatedGroupLang $e){
				$error = new error();
				$error->setCode('translated.group.lang.empty');
				$view->addError($error);
			}
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function add($data){
		authorization::haveOrFail('group_add');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\group\\add");
		$inputsRules = array(
			'avatar' => array(
				'type' => 'file',
				'optional' => true,
				'empty' => true
			),
			'group-lang' => array(
				'type' => 'string'
			),
			'title' => array(
				'type' => 'string'
			)
		);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$inputs = $this->checkinputs($inputsRules);
				if(!in_array($inputs['group-lang'], translator::$allowlangs))
					throw new inputValidation("lang");
				$group = new group();
				$group->lang = $inputs['group-lang'];
				if($inputs["avatar"]['error'] == 0){
					$type = getimagesize($inputs["avatar"]['tmp_name']);
					if(in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
						$title = IO\md5($inputs["avatar"]['tmp_name']);
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
						$directory = packages::package('ghafiye')->getFilePath("storage/public/groups/".$title.$type_name);
						if(move_uploaded_file($inputs["avatar"]['tmp_name'], $directory)){
							$group->avatar = "storage/public/groups/".$title.$type_name;
						}else{
							throw new inputValidation($inputs["avatar"]);
						}
					}else{
						throw new inputValidation($inputs["avatar"]);
					}
				}elseif($inputs["avatar"]['error'] != 4){
					throw new inputValidation("avatar");
				}
				$group->save();
				$group->addTitle($inputs['title'], $inputs['group-lang']);
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("groups/edit/".$group->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('group_delete');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\group\\delete");
		$group = group::byId($data['id']);
		if(!$group){
			throw new NotFound();
		}
		$view->setGroup($group);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$group->delete();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("groups"));
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
class translatedGroupLang extends \Exception{}
