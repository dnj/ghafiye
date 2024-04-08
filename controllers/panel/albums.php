<?php
namespace packages\ghafiye\controllers\panel;
use \packages\base\{IO, db, http, NotFound, packages, view\error, translator, db\parenthesis, inputValidation, views\FormError};
use \packages\userpanel;
use \packages\userpanel\{controller, log};
use \packages\ghafiye\{view, song, album, authorization, authentication, logs};

class albums extends controller{
	protected $authentication = true;
	public function listview(){
		authorization::haveOrFail('albums_list');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\album\\listview");
		$album = new album();
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'musixmatch_id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'lang' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'song' => array(
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
			foreach(array('id', 'lang', 'musixmatch_id') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id', 'musixmatch_id'))){
						$comparison = 'equals';
					}
					$album->where($item, $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['song']) and $inputs['song']){
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_songs.id", $inputs['song'], 'equals', "OR");
				$album->where($parenthesis);
				db::join("ghafiye_songs", "ghafiye_songs.album=ghafiye_albums.id", "INNER");
				db::setQueryOption("DISTINCT");
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				$parenthesis->where("ghafiye_albums_titles.title", $inputs['word'], $inputs['comparison']);
				$album->where($parenthesis);
				db::join("ghafiye_albums_titles", "ghafiye_albums_titles.album=ghafiye_albums.id", "INNER");
				db::setQueryOption("DISTINCT");
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$album->pageLimit = $this->items_per_page;
		$albums = $album->paginate($this->page, array("ghafiye_albums.*"));
		$this->total_pages = $album->totalPages;
		$view->setDataList($albums);
		$view->setPaginate($this->page, $album->totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('album_edit');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\album\\edit");
		$album = album::byId($data['id']);
		if(!$album)
			throw new NotFound;
		$view->setAlbum($album);
		$inputsRules = array(
			'avatar' => array(
				'type' => 'file',
				'empty' => true,
				'optional' => true
			),
			'musixmatch_id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'album-lang' => array(
				'type' => 'string',
				'optional' => true
			),
			'titles' => array(),
			'songs' => array(
				'optional' => true,
				'empty' => true
			)
		);
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
				if(!array_key_exists("songs", $inputs) or !is_array($inputs['songs'])){
					$inputs['songs'] = array();
				}
				foreach($inputs['songs'] as $key => $song){
					$song = song::byId($song);
					if(!$song){
						throw new inputValidation("songs[{$key}]");
					}
				}
				if(!isset($inputs['titles'][$inputs['album-lang']])){
					throw new translatedAlbumLang();
				}
				$parameters = ['oldData' => []];
				foreach($album->titles as $title){
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
					if($title){
						$album->addTitle($title, $lang, album\title::published);
					}else{
						throw new inputValidation("titles[{$lang}]");
					}
				}

				foreach($album->songs as $song){
					if(($key = array_search($song->id, $inputs['songs'])) === false){
						$parameters['oldData']['songs'][] = $song;
						$song->album = null;
						$song->save();
					}else{
						unset($inputs['songs'][$key]);
					}
				}
				foreach($inputs['songs'] as $song){
					$song = song::byId($song);
					$song->album = $album->id;
					$song->save();
				}
				if(isset($inputs['album-lang']) and $inputs['album-lang']){
					if(!in_array($inputs['album-lang'], translator::$allowlangs)){
						throw new inputValidation("album-lang");
					}
					if($album->lang != $inputs['album-lang']){
						$parameters['oldData']['album-lang'] = $album->lang;
						$album->lang = $inputs['album-lang'];
					}
				}
				if(isset($inputs['musixmatch_id']) and $inputs['musixmatch_id'] and $album->musixmatch_id != $inputs['musixmatch_id']){
					$parameters['oldData']['musixmatch_id'] = $album->musixmatch_id;
					$album->musixmatch_id = $inputs['musixmatch_id'];
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
						$directory = packages::package('ghafiye')->getFilePath("storage/public/albums/".$title.$type_name);
						if(move_uploaded_file($inputs["avatar"]['tmp_name'], $directory)){
							$album->image = "storage/public/albums/".$title.$type_name;
						}else{
							throw new inputValidation($inputs["avatar"]);
						}
					}else{
						throw new inputValidation($inputs["avatar"]);
					}
				}elseif($inputs["avatar"]['error'] != 4){
					throw new inputValidation("avatar");
				}
				$album->status = album::accepted;
				$album->save();

				$log = new log();
				$log->user = authentication::getID();
				$log->title = translator::trans("ghafiye.logs.album.edit", ['album_id' => $album->id, 'album_title' => $album->title()]);
				$log->type = logs\albums\edit::class;
				$log->parameters = $parameters;
				$log->save();

				$this->response->setStatus(true);
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(translatedAlbumLang $e){
				$error = new error();
				$error->setCode('translated.album.lang.empty');
				$error->setMessage(translator::trans('error.translated.album.lang.empty'));
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
		authorization::haveOrFail('album_add');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\album\\add");
		$inputsRules = [
			'avatar' => [
				'type' => 'file',
				'empty' => true
			],
			'musixmatch_id' => [
				'type' => 'number',
				'empty' => true
			],
			'album-lang' => [
				'type' => 'string',
				'values' => translator::$allowlangs
			],
			'titles' => [],
			'songs' => []
		];
		if(http::is_post()){
			try{
				$inputs = $this->checkinputs($inputsRules);
				if(!is_array($inputs['titles'])){
					throw new inputValidation("titles");
				}
				if(!is_array($inputs['songs'])){
					throw new inputValidation("songs");
				}
				if(!isset($inputs['titles'][$inputs['album-lang']])){
					throw new translatedAlbumLang();
				}
				foreach($inputs['titles'] as $lang => $title){
					if(!$title){
						throw new inputValidation("titles[{$lang}]");
					}
				}
				$songs = [];
				foreach($inputs['songs'] as $key => $song){
					if(!$song = song::byId($song)){
						throw new inputValidation("songs[{$key}]");
					}
					$songs[] = $song;
				}
				if($inputs["avatar"]['error'] == 0){
					if(!in_array(getimagesize($inputs["avatar"]['tmp_name'])[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
						throw new inputValidation($inputs["avatar"]);
					}
				}elseif($inputs["avatar"]['error'] == 4){
					unset($inputs["avatar"]);
				}else{
					throw new inputValidation("avatar");
				}
				$album = new album();
				$album->lang = $inputs['album-lang'];
				if(isset($inputs['musixmatch_id']) and $inputs['musixmatch_id']){
					$album->musixmatch_id = $inputs['musixmatch_id'];
				}
				if(isset($inputs["avatar"])){
					$title = IO\md5($inputs["avatar"]['tmp_name']);
					switch(getimagesize($inputs["avatar"]['tmp_name'])[2]){
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
					$directory = packages::package('ghafiye')->getFilePath("storage/public/albums/".$title.$type_name);
					if(!move_uploaded_file($inputs["avatar"]['tmp_name'], $directory)){
						throw new inputValidation($inputs["avatar"]);
					}
					$album->image = "storage/public/albums/".$title.$type_name;
				}
				$album->status = album::accepted;
				$album->save();
				foreach($inputs['titles'] as $lang => $title){
					$album->addTitle($title, $lang, album\title::published);
				}
				foreach($songs as $song){
					$song->album = $album->id;
					$song->save();
				}

				$log = new log();
				$log->user = authentication::getID();
				$log->title = translator::trans("ghafiye.logs.album.add", ['album_id' => $album->id, 'album_title' => $album->title()]);
				$log->type = logs\albums\add::class;
				$log->save();

				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("albums/edit/{$album->id}"));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(translatedAlbumLang $e){
				$error = new error();
				$error->setCode('translated.album.lang.empty');
				$error->setMessage(translator::trans('error.translated.album.lang.empty'));
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
		authorization::haveOrFail('album_delete');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\album\\delete");
		$album = album::byId($data['id']);
		if(!$album)
			throw new NotFound();
		$view->setAlbum($album);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{
				$log = new log();
				$log->user = authentication::getID();
				$log->title = translator::trans("ghafiye.logs.album.delete", ['album_id' => $album->id, 'album_title' => $album->title()]);
				$log->type = logs\albums\delete::class;
				$log->parameters = ['album' => $album];
				$log->save();

				$album->delete();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("albums"));
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

class translatedAlbumLang extends \Exception{}
