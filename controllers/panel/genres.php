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
use \packages\ghafiye\genre;
use \packages\ghafiye\authorization;

class genres extends controller{
	protected $authentication = true;
	public function listview(){
		authorization::haveOrFail('genres_list');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\genre\\listview");
		$genre = new genre();
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'song' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'musixmatch_id' => array(
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
			foreach(array('id', 'musixmatch_id') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id', 'musixmatch_id'))){
						$comparison = 'equals';
					}
					$genre->where($item, $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['song']) and $inputs['song']){
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_songs.id", $inputs['song'], 'equals', "OR");
				$genre->where($parenthesis);
				db::join("ghafiye_songs", "ghafiye_songs.genre=ghafiye_genres.id", "INNER");
				db::setQueryOption("DISTINCT");
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_genres_titles.title", $inputs['word'], $inputs['comparison']);
				$genre->where($parenthesis);
				db::join("ghafiye_genres_titles", "ghafiye_genres_titles.genre=ghafiye_genres.id", "INNER");
				db::setQueryOption("DISTINCT");
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$genre->pageLimit = $this->items_per_page;
		$genres = $genre->paginate($this->page, array("ghafiye_genres.*"));
		$this->total_pages = $genre->totalPages;
		$view->setDataList($genres);
		$view->setPaginate($this->page, $genre->totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('genre_edit');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\genre\\edit");
		$genre = genre::byId($data['id']);
		if(!$genre){
			throw new NotFound;
		}
		$view->setGenre($genre);
		$inputsRules = array(
			'musixmatch_id' => array(
				'type' => 'number',
				'empty' => true,
				'optional' => true
			),
			'titles' => array()
		);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$inputs = $this->checkinputs($inputsRules);
				if(is_array($inputs['titles'])){
					foreach($inputs['titles'] as $lang => $title){
						if(!in_array($lang, translator::$allowlangs) or !$title){
							throw new inputValidation("titles[{$lang}]");
						}
					}
				}else{
					throw new inputValidation("titles");
				}
				foreach($genre->titles as $title){
					if(isset($inputs['titles'][$title->lang])){
						if($inputs['titles'][$title->lang] != $title->name){
							$title->name = $inputs['titles'][$title->lang];
							$title->save();
						}
						unset($inputs['titles'][$title->lang]);
					}else{
						$title->delete();
					}
				}
				foreach($inputs['titles'] as $lang => $title){
					if($title){
						$genre->addTitle($title, $lang);
					}else{
						throw new inputValidation("titles[{$lang}]");
					}
				}
				if(isset($inputs['musixmatch_id']) and $inputs['musixmatch_id']){
					$genre->musixmatch_id = $inputs['musixmatch_id'];
				}
				$genre->save();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("genres/edit/{$genre->id}"));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function add(){
		authorization::haveOrFail('genre_add');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\genre\\add");
		$inputsRules = array(
			'musixmatch_id' => array(
				'type' => 'number',
				'empty' => true,
				'optional' => true
			),
			'titles' => array()
		);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$inputs = $this->checkinputs($inputsRules);
				if(is_array($inputs['titles'])){
					foreach($inputs['titles'] as $lang => $title){
						if(!in_array($lang, translator::$allowlangs) or !$title){
							throw new inputValidation("titles[{$lang}]");
						}
					}
				}else{
					throw new inputValidation("titles");
				}
				$genre = new genre();
				if(isset($inputs['musixmatch_id']) and $inputs['musixmatch_id']){
					$genre->musixmatch_id = $inputs['musixmatch_id'];
				}else{
					$genre->musixmatch_id = null;
				}
				$genre->save();
				foreach($inputs['titles'] as $lang => $title){
					if($title){
						$genre->addTitle($title, $lang);
					}else{
						throw new inputValidation("titles[{$lang}]");
					}
				}
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("genres/edit/{$genre->id}"));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('genre_delete');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\genre\\delete");
		$genre = genre::byId($data['id']);
		if(!$genre)
			throw new NotFound;
		$view->setGenre($genre);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$genre->delete();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("genres"));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
}
