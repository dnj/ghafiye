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
use \packages\ghafiye\song;
use \packages\ghafiye\authorization;

class songs extends controller{
	protected $authentication = true;
	public function listview(){
		authorization::haveOrFail('songs_list');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\song\\listview");
		$song = new song();
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'group' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'album' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'person' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'lang' => array(
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
			foreach(array('id', 'lang', 'group', 'album') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id', 'group', 'album'))){
						$comparison = 'equals';
					}
					$song->where("`{$item}`", $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['person']) and $inputs['person']){
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_songs_persons.person", $inputs['person'], 'equals');
				$song->where($parenthesis);
				db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "INNER");
				db::setQueryOption("DISTINCT");
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_songs_titles.title", $inputs['word'], $inputs['comparison']);
				$song->where($parenthesis);
				db::join("ghafiye_songs_titles", "ghafiye_songs_titles.album=ghafiye_songs.id", "INNER");
				db::setQueryOption("DISTINCT");
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$song->pageLimit = $this->items_per_page;
		$songs = $song->paginate($this->page);
		$this->total_pages = $song->totalPages;
		$view->setDataList($songs);
		$view->setPaginate($this->page, $song->totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('song_delete');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\song\\delete");
		$song = song::byId($data['id']);
		if(!$song)
			throw new NotFound();
		$view->setSong($song);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$song->delete();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("songs"));
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
