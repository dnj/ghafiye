<?php
namespace packages\ghafiye\controllers\panel;
use \packages\base;
use \packages\base\{IO, db, http, NotFound, packages, view\error, translator, inputValidation, views\FormError, db\parenthesis, db\duplicateRecord};
use \packages\userpanel;
use \packages\userpanel\{controller, log};
use \packages\ghafiye\{view, group, person, group\title, authorization, authentication, views\panel\group as vGroup, logs};

class groups extends controller{
	protected $authentication = true;
	public function listview(){
		authorization::haveOrFail('groups_list');
		$view = view::byName(vGroup\listview::class);
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
			'person' => array(
				'type' => 'number',
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
			foreach(array('id', 'lang') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id'))){
						$comparison = 'equals';
					}
					$group->where("`$item`", $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['person']) and $inputs['person']){
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_groups_persons.person", $inputs['person'], 'equals', "OR");
				$group->where($parenthesis);
				db::join("ghafiye_groups_persons", "ghafiye_groups_persons.group_id=ghafiye_groups.id", "INNER");
				db::setQueryOption("DISTINCT");
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_groups_titles.title", $inputs['word'], $inputs['comparison']);
				$group->where($parenthesis);
				db::join("ghafiye_groups_titles", "ghafiye_groups_titles.group_id=ghafiye_groups.id", "INNER");
				db::setQueryOption("DISTINCT");
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$group->pageLimit = $this->items_per_page;
		$groups = $group->paginate($this->page, array("ghafiye_groups.*"));
		$this->total_pages = $group->totalPages;
		$view->setDataList($groups);
		$view->setPaginate($this->page, $group->totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('group_edit');
		$view = view::byName(vGroup\edit::class);
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
			'titles' => array(
				'optional' => true
			),
			'persons' => array(
				'optional' => true
			),
			'cover' => [
				'type' => 'file',
				'optional' => true,
				'empty' => true
			]
		);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$inputs = $this->checkinputs($inputsRules);
				foreach(array_keys($inputsRules) as $item){
					if(isset($inputs[$item]) and !$inputs[$item]){
						unset($inputs[$item]);
					}
				}
				if(isset($inputs['group-lang'])){
					if(!in_array($inputs['group-lang'], translator::$allowlangs)){
						throw new inputValidation("group-lang");
					}
				}else{
					$inputs['group-lang'] = $group->lang;
				}
				if(isset($inputs['titles'])){
					if(is_array($inputs['titles'])){
						if(isset($inputs['group-lang'])){
							if(!isset($inputs['titles'][$inputs['group-lang']])){
								throw new translatedGroupLang();
							}
						}
						foreach($inputs['titles'] as $key => $title){
							if(!in_array($key, translator::$allowlangs) or !$title){
								throw new inputValidation("titles[{$key}]");
							}
						}
						$obj = new title();
						$obj->where('title', $title);
						$obj->where('group_id', $group->id, '!=');
						if($obj->has()){
							throw new duplicateTitleRecord($title);
						}
						$person = new person\name();
						if($person->byName($title)){
							throw new duplicateTitleRecord($title);
						}
					}else{
						throw new inputValidation("titles");
					}
				}
				if(isset($inputs['persons'])){
					if(!is_array($inputs['persons'])){
						throw new inputValidation("persons");
					}
					foreach($inputs['persons'] as $key => $person){
						if(!person::byId($person)){
							throw new inputValidation("persons[{$key}]");
						}
					}
				}
				if(isset($inputs["avatar"])){
					if($inputs["avatar"]['error'] == 0){
						$type = getimagesize($inputs["avatar"]['tmp_name']);
						if(!in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
							throw new inputValidation("avatar");
						}
					}elseif($inputs["avatar"]['error'] == 4){
						unset($inputs["avatar"]);
					}else{
						throw new inputValidation("avatar");
					}
				}
				if(isset($inputs["cover"])){
					if($inputs["cover"]['error'] == 0){
						$type = getimagesize($inputs["cover"]['tmp_name']);
						if(!in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
							throw new inputValidation("cover");
						}
					}elseif($inputs["cover"]['error'] == 4){
						unset($inputs["cover"]);
					}else{
						throw new inputValidation("cover");
					}
				}
				if(isset($inputs["avatar"])){
					$type = getimagesize($inputs["avatar"]['tmp_name']);
					$title = IO\md5($inputs["avatar"]['tmp_name']);
					switch($type[2]){
						case(IMAGETYPE_JPEG):
							$type_name = 'jpg';
							break;
						case(IMAGETYPE_GIF):
							$type_name = 'gif';
							break;
						case(IMAGETYPE_PNG):
							$type_name = 'png';
							break;
					}
					$directory = packages::package('ghafiye')->getFilePath("storage/public/groups/{$title}.{$type_name}");
					if(!move_uploaded_file($inputs["avatar"]['tmp_name'], $directory)){
						throw new inputValidation("avatar");
					}
					$inputs["avatar"] = "storage/public/groups/{$title}.{$type_name}";
				}

				if(isset($inputs["cover"])){
					$type = getimagesize($inputs["cover"]['tmp_name']);
					$title = IO\md5($inputs["cover"]['tmp_name']);
					switch($type[2]){
						case(IMAGETYPE_JPEG):
							$type_name = 'jpg';
							break;
						case(IMAGETYPE_GIF):
							$type_name = 'gif';
							break;
						case(IMAGETYPE_PNG):
							$type_name = 'png';
							break;
					}
					$directory = packages::package('ghafiye')->getFilePath("storage/public/groups/{$title}.{$type_name}");
					if(!move_uploaded_file($inputs["cover"]['tmp_name'], $directory)){
						throw new inputValidation("cover");
					}
					$inputs["cover"] = "storage/public/groups/{$title}.{$type_name}";
				}

				$parameters = ['oldData' => []];
				if(isset($inputs['titles'])){
					foreach($group->titles as $title){
						if(isset($inputs['titles'][$title->lang])){
							if($inputs['titles'][$title->lang] != $title->title){
								$parameters['oldData']['titles'][] = $title;
								$title->title = $inputs['titles'][$title->lang];
								$title->save();
							}
							unset($inputs['titles'][$title->lang]);
						}else{
							$parameters['oldData']['titles'][] = $title;
							$title->delete();
						}
					}
					foreach($inputs['titles'] as $lang => $title){
						$group->addTitle($title, $lang);
					}
				}
				if(isset($inputs['persons'])){
					foreach($group->persons as $person){
						if(($key = array_search($person->person->id, $inputs['persons'])) === false){
							$parameters['oldData']['persons'][] = $person;
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
				}

				if(isset($inputs['group-lang']) and $group->lang != $inputs['group-lang']){
					$parameters['oldData']['group-lang'] = $group->lang;
					$group->lang = $inputs['group-lang'];
				}
				foreach(['avatar', 'cover'] as $item){
					if(isset($inputs[$item])){
						$group->$item = $inputs[$item];
					}
				}
				$group->save();

				$log = new log();
				$log->user = authentication::getID();
				$log->title = translator::trans("ghafiye.logs.group.edit", ['group_id' => $group->id, 'group_title' => $group->title()]);
				$log->type = logs\groups\edit::class;
				$log->parameters = $parameters;
				$log->save();

				$this->response->setStatus(true);
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(duplicateTitleRecord $e){
				$error = new error();
				$error->setCode('ghafiye.panel.group.duplicateTitleRecord');
				$error->setMessage(translator::trans('error.ghafiye.panel.group.duplicateTitleRecord', ['title' => $e->getTitle()]));
				$view->addError($error);
			}catch(translatedGroupLang $e){
				$error = new error();
				$error->setMessage(translator::trans('error.translated.group.lang.empty'));
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
		$view = view::byName(vGroup\add::class);
		$inputsRules = array(
			'avatar' => array(
				'type' => 'file',
				'optional' => true,
				'empty' => true
			),
			'group-lang' => array(
				'type' => 'string',
				'values' => translator::$allowlangs
			),
			'titles' => array(),
			'persons' => array(
				'empty' => true
			),
			'cover' => [
				'type' => 'file',
				'optional' => true,
				'empty' => true
			]
		);
		if(http::is_post()){
			$this->response->setStatus(false);
			try{
				$inputs = $this->checkinputs($inputsRules);
				$group = new group();
				if(is_array($inputs['titles'])){
					foreach($inputs['titles'] as $key => $title){
						if(!in_array($key, translator::$allowlangs) or !$title){
							throw new inputValidation("titles[{$key}]");
						}
						$obj = new title();
						$obj->where('title', $title);
						if($obj->has()){
							throw new duplicateTitleRecord($title);
						}
						$person = new person\name();
						if($person->byName($title)){
							throw new duplicateTitleRecord($title);
						}
					}
				}else{
					throw new inputValidation("titles");
				}

				if($inputs["avatar"]['error'] == 0){
					$type = getimagesize($inputs["avatar"]['tmp_name']);
					if(!in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
						throw new inputValidation($inputs["avatar"]);
					}
				}elseif($inputs["avatar"]['error'] == 4){
					unset($inputs['avatar']);
				}else{
					throw new inputValidation("avatar");
				}
				if($inputs["cover"]['error'] == 0){
					$type = getimagesize($inputs["cover"]['tmp_name']);
					if(!in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
						throw new inputValidation($inputs["cover"]);
					}
				}elseif($inputs["cover"]['error'] == 4){
					unset($inputs['cover']);
				}else{
					throw new inputValidation("cover");
				}
				if(!isset($inputs["persons"]) or !is_array($inputs['persons'])){
					$inputs['persons'] = [];
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
				$group->lang = $inputs['group-lang'];
				if(isset($inputs['avatar'])){
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
						$inputs["avatar"] = "storage/public/groups/".$title.$type_name;
					}else{
						throw new inputValidation($inputs["avatar"]);
					}
					$group->avatar = $inputs["avatar"];
				}
				if(isset($inputs['cover'])){
					$title = IO\md5($inputs["cover"]['tmp_name']);
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
					if(move_uploaded_file($inputs["cover"]['tmp_name'], $directory)){
						$inputs["cover"] = "storage/public/groups/".$title.$type_name;
					}else{
						throw new inputValidation($inputs["cover"]);
					}
					$group->cover = $inputs["cover"];
				}
				$group->save();
				foreach($inputs['persons'] as $person){
					$person = new group\person(array(
						'group_id' => $group->id,
						'person' => $person
					));
					$person->save();
				}
				foreach($inputs['titles'] as $lang => $title){
					$group->addTitle($title, $lang);
				}

				$log = new log();
				$log->user = authentication::getID();
				$log->title = translator::trans("ghafiye.logs.group.add", ['group_id' => $group->id, 'group_title' => $group->title()]);
				$log->type = logs\groups\add::class;
				$log->save();

				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("groups/edit/".$group->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(duplicateTitleRecord $e){
				$error = new error();
				$error->setCode('ghafiye.panel.group.duplicateTitleRecord');
				$error->setMessage(translator::trans('error.ghafiye.panel.group.duplicateTitleRecord', ['title' => $e->getTitle()]));
				$view->addError($error);
			}catch(translatedGroupLang $e){
				$error = new error();
				$error->setCode('translated.group.lang.empty');
				$error->setMessage(translator::trans('error.translated.group.lang.empty'));
				$view->addError($error);
			}
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('group_delete');
		$view = view::byName(vGroup\delete::class);
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
class duplicateTitleRecord extends duplicateRecord{
	private $title;
	public function __construct($title, string $message = ''){
		$this->title = $title;
		parent::__construct($message);
	}
	public function getTitle(){
		return $this->title;
	}
}
