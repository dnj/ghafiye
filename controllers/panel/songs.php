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
use \packages\base\db\duplicateRecord;

use \packages\userpanel;
use \packages\userpanel\controller;

use \packages\ghafiye\view;
use \packages\ghafiye\song;
use packages\ghafiye\album;
use packages\ghafiye\group;
use \packages\ghafiye\genre;
use \packages\ghafiye\person;
use packages\ghafiye\song\lyric;
use packages\ghafiye\song\person as songPerson;
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
				db::join("ghafiye_songs_titles", "ghafiye_songs_titles.song=ghafiye_songs.id", "INNER");
				db::setQueryOption("DISTINCT");
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$song->pageLimit = $this->items_per_page;
		$songs = $song->paginate($this->page, "ghafiye_songs.*");
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
	private function is_validTime(string $time){
		if(strpos($time, ':') === false)return false;
		list($min, $sec) = explode(":",$time, 2);
		if(is_numeric($min) and is_numeric($sec)){
			return $min * 60 + $sec;
		}
		return false;
	}
	public function edit($data){
		authorization::haveOrFail('song_edit');
		$song = song::byId($data['song']);
		if(!$song){
			throw new NotFound();
		}
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\song\\edit");
		$view->setSong($song);
		$allowlangs = translator::$allowlangs;
		$view->setAllowLangs($allowlangs);
		$view->setGenres(genre::get());
		$this->response->setStatus(false);
		if(http::is_post()){
			$inputsRules = array(
				'persons' => array(
					'optinal' => true
				),
				'musixmatch_id' => array(
					'type' => 'string',
					'optinal' => true,
					'empty' => true
				),
				'spotify_id' => array(
					'type' => 'string',
					'optinal' => true,
					'empty' => true
				),
				'album' => array(
					'type' => 'string',
					'optinal' => true,
					'empty' => true
				),
				'group' => array(
					'type' => 'string',
					'optinal' => true,
					'empty' => true
				),
				'duration' => array(
					'type' => 'string',
					'optinal' => true,
				),
				'genre' => array(
					'type' => 'string',
					'optinal' => true
				),
				'lang' => array(
					'type' => 'string',
					'optinal' => true,
					'values' => $allowlangs
				),
				'image' => array(
					'type' => 'file',
					'optional' => true,
					'empty' => true
				),
				'status' => array(
					'type' => 'number',
					'optinal' => true,
					'values' => array(song::publish, song::draft)
				),
				'lyric_lang' => array(
					'type' => 'string',
					'optinal' => true,
					'values' => $allowlangs
				),
				'titles' => array(
					'optinal' => true
				),
				'lyric' => array(
					'optinal' => true
				)
			);
			try{
				$lyricIDs = [];
				$inputs = $this->checkinputs($inputsRules);
				if(isset($inputs['persons'])){
					if(is_array($inputs['persons'])){
						foreach($inputs['persons'] as $key => $person){
							$personObj = person::byId($person['id']);
							if(!$personObj){
								throw new inputValidation("persons[{$key}][id]");
							}
							if(!isset($inputs['persons'][$key]['role'])){
								throw new inputValidation("persons[{$key}][id]");
							}
							if(!in_array($inputs['persons'][$key]['role'], array(songPerson::singer, songPerson::writer, songPerson::composer))){
								throw new inputValidation("persons[{$key}][role]");
							}
						}
					}else{
						throw new inputValidation("persons");
					}
				}
				if(isset($inputs['titles'])){
					if(is_array($inputs['titles'])){
						foreach($inputs['titles'] as $lang => $title){
							if(!in_array($lang, $allowlangs) or !$title){
								throw new inputValidation("titles[{$lang}]");
							}
						}
					}else{
						throw new inputValidation("titles");
					}
				}
				if(isset($inputs['lyric'])){
					if(!isset($inputs['lyric_lang'])){
						throw new inputValidation("lyric_lang");
					}
					$isOriginalLyric = ($inputs['lyric_lang'] == $song->lang);
					if(is_array($inputs['lyric'])){
						foreach($inputs['lyric'] as $key => $lyric){
							if($isOriginalLyric){
								if(!isset($lyric['time']) or ($time = $this->is_validTime($lyric['time'])) < 0 ){
									throw new inputValidation("lyric[{$key}][time]");
								}
								$inputs['lyric'][$key]['time'] = $time;
								if(!isset($lyric['text']) or !$lyric['text']){
									throw new inputValidation("lyric[{$key}][text]");
								}
							}
							if(isset($lyric['id']) and $lyric['id']){
								$inputs['lyric'][$key]['obj'] = lyric::where("song", $song->id)->where("id", $lyric['id'])->getOne();
								if(!$inputs['lyric'][$key]['obj']){
									throw new inputValidation("lyric[{$key}][id]");
								}
								$lyricIDs[] = $lyric['id'];
							}elseif(isset($lyric['parent']) and $lyric['parent']){
								$inputs['lyric'][$key]['parent'] = lyric::where("song", $song->id)->where("id", $lyric['parent'])->getOne();
								if(!$inputs['lyric'][$key]['parent']){
									throw new inputValidation("lyric[{$key}][parent]");
								}
							}
						}
					}else{
						throw new inputValidation("lyric");
					}
				}
				if(isset($inputs['album'])){
					if($inputs['album']){
						$album = album::byId($inputs['album']);
						if(!$album){
							throw new inputValidation('album');
						}
					}else{
						unset($inputs['album']);
					}
				
				}
				if(isset($inputs['group'])){
					if($inputs['group']){
						$group = group::byId($inputs['group']);
						if(!$group){
							throw new inputValidation('group');
						}
					}else{
						unset($inputs['group']);
					}
				}
				if(isset($inputs['genre'])){
					if($inputs['genre']){
						$genre = genre::byId($inputs['genre']);
						if(!$genre){
							throw new inputValidation('genre');
						}
					}else{
						unset($inputs['genre']);
					}
				}
				if(isset($inputs['image'])){
					if($inputs["image"]['error'] == 0){
						$type = getimagesize($inputs["image"]['tmp_name']);
						if(in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
							$title = IO\md5($inputs["image"]['tmp_name']);
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
							$directory = packages::package('ghafiye')->getFilePath("storage/public/songs/".$title.$type_name);
							if(move_uploaded_file($inputs["image"]['tmp_name'], $directory)){
								$inputs["image"] = "storage/public/songs/".$title.$type_name;
							}else{
								throw new inputValidation($inputs["image"]);
							}
						}else{
							throw new inputValidation($inputs["image"]);
						}
					}elseif($inputs["image"]['error'] == 4){
						unset($inputs["image"]);
					}else{
						throw new inputValidation("image");
					}
				}
				foreach(array('musixmatch_id', 'spotify_id') as $key){
					if(isset($inputs[$key]) and $inputs[$key]){
						$song->$key = $inputs[$key];
					}else{
						$song->$key = null;
					}
				}
				foreach(array('lang', 'status', 'group', 'album', 'image', 'genre') as $key){
					if(isset($inputs[$key]) and $inputs[$key]){
						$song->$key = $inputs[$key];
					}
				}
				$song->save();
				if(isset($inputs['titles'])){
					foreach($song->titles as $title){
						if(!isset($inputs['titles'][$title->lang])){
							$title->delete();
						}else{
							$title->title = $inputs['titles'][$title->lang];
							$title->save();
						}
					}
					foreach($inputs['titles'] as $lang => $title){
						$song->setTitle($title, $lang);
					}
				}
				if(isset($inputs['persons'])){
					foreach($song->persons as $person){
						if(!isset($inputs['persons'][$person->id])){
							$person->delete();
						}else{
							$person->primary = isset($inputs['persons'][$person->id]['primary']);
							$person->role = $inputs['persons'][$person->id]['role'];
							$person->save();
						}
					}
					foreach($inputs['persons'] as $key => $person){
						$songPerson = new songPerson();
						$songPerson->song = $song->id;
						$songPerson->person = $person['id'];
						$songPerson->primary = isset($inputs['persons'][$key]['primary']);
						$songPerson->role = $inputs['persons'][$key]['role'];
						$songPerson->save();
					}
				}
				if(isset($inputs['lyric'])){
					foreach($song->getLyricByLang($inputs['lyric_lang']) as $lyric){
						if(!in_array($lyric->id, $lyricIDs)){
							$lyric->delete();
						}
					}
					foreach($inputs['lyric'] as $lyric){
						if(isset($lyric['parent']) and $lyric['parent'] instanceof lyric){
							$lyric['obj'] = new lyric();
							$lyric['obj']->song = $song->id;
							$lyric['obj']->lang = $inputs['lyric_lang'];
							$lyric['obj']->parent = $lyric['parent']->id;
						}elseif(!isset($lyric['obj'])){
							$lyric['obj'] = new lyric();
							$lyric['obj']->song = $song->id;
							$lyric['obj']->lang = $inputs['lyric_lang'];
						}
						if(isset($lyric['obj'])){
							$lyric['obj']->time = $isOriginalLyric ? $lyric['time'] : 0;
							$lyric['obj']->text = $lyric['text'];
							$lyric['obj']->save();
						}
					}
				}
				$this->response->setStatus(true);
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
		authorization::haveOrFail('song_add');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\song\\add");
		$allowlangs = translator::$allowlangs;
		$view->setAllowLangs($allowlangs);
		$view->setGenres(genre::get());
		$this->response->setStatus(false);
		if(http::is_post()){
			$inputsRules = array(
				'musixmatch_id' => array(
					'type' => 'number',
					'optional' => true,
					'empty' => true
				),
				'spotify_id' => array(
					'type' => 'string',
					'optional' => true,
					'empty' => true
				),
				'album' => array(
					'type' => 'number',
					'optinal' => true,
					'empty' => true
				),
				'group' => array(
					'type' => 'number',
					'optinal' => true,
					'empty' => true
				),
				'duration' => array(
					'type' => 'number'
				),
				'genre' => array(
					'type' => 'number',
				),
				'lang' => array(
					'type' => 'string',
					'values' => $allowlangs
				),
				'image' => array(
					'type' => 'file',
					'optional' => true,
					'empty' => true
				),
				'status' => array(
					'type' => 'number',
					'values' => array(song::publish, song::draft)
				),
				'title' => array(
					'type' => 'string',
				),
				'lyric' => array(),
				'persons' => array()
			);
			try{
				$inputs = $this->checkinputs($inputsRules);
				if(is_array($inputs['persons'])){
					foreach($inputs['persons'] as $key => $person){
						$personObj = person::byId($person['id']);
						if(!$personObj){
							throw new inputValidation("persons[{$key}][id]");
						}
						if(!isset($inputs['persons'][$key]['role'])){
							throw new inputValidation("persons[{$key}][id]");
						}
						if(!in_array($inputs['persons'][$key]['role'], array(songPerson::singer, songPerson::writer, songPerson::composer))){
							throw new inputValidation("persons[{$key}][role]");
						}
					}
				}else{
					throw new inputValidation("persons");
				}
				if(is_array($inputs['lyric'])){
					foreach($inputs['lyric'] as $key => $lyric){
						if(!isset($lyric['time']) or ($time = $this->is_validTime($lyric['time'])) < 0 ){
							throw new inputValidation("lyric[{$key}][time]");
						}
						$inputs['lyric'][$key]['time'] = $time;
					}
				}else{
					throw new inputValidation("lyric");
				}
				if(isset($inputs['duration'])){
					if($inputs['duration'] <= 0){
						throw new inputValidation('duration');
					}
				}
				$genre = genre::byId($inputs['genre']);
				if(!$genre){
					throw new inputValidation('genre');
				}
				if(isset($inputs['album'])){
					if($inputs['album']){
						$album = album::byId($inputs['album']);
						if(!$album){
							throw new inputValidation('album');
						}
					}else{
						unset($inputs['album']);
					}
				}
				if(isset($inputs['group'])){
					if($inputs['group']){
						$group = group::byId($inputs['group']);
						if(!$group){
							throw new inputValidation('group');
						}
					}else{
						unset($inputs['group']);
					}
				}
				if(isset($inputs['image'])){
					if($inputs["image"]['error'] == 0){
						$type = getimagesize($inputs["image"]['tmp_name']);
						if(in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
							$title = IO\md5($inputs["image"]['tmp_name']);
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
							$directory = packages::package('ghafiye')->getFilePath("storage/public/songs/".$title.$type_name);
							if(move_uploaded_file($inputs["image"]['tmp_name'], $directory)){
								$inputs["image"] = "storage/public/songs/".$title.$type_name;
							}else{
								throw new inputValidation($inputs["image"]);
							}
						}else{
							throw new inputValidation($inputs["image"]);
						}
					}elseif($inputs["image"]['error'] == 4){
						unset($inputs["image"]);
					}else{
						throw new inputValidation("image");
					}
				}
				$song = new song();
				foreach(array('lang', 'status', 'genre', 'duration') as $key){
					$song->$key = $inputs[$key];
				}
				foreach(array('musixmatch_id', 'spotify_id', 'group', 'album', 'image') as $key){
					if(isset($inputs[$key]) and $inputs[$key]){
						$song->$key = $inputs[$key];
					}
				}
				$song->save();
				$song->setTitle($inputs['title'], $inputs['lang']);
				foreach($inputs['persons'] as $key => $person){
					$songPerson = new songPerson();
					$songPerson->song = $song->id;
					$songPerson->person = $person['id'];
					$songPerson->primary = isset($inputs['persons'][$key]['primary']);
					$songPerson->role = $inputs['persons'][$key]['role'];
					$songPerson->save();
				}
				foreach($inputs['lyric'] as $lyr){
					$lyric = new lyric();
					$lyric->song = $song->id;
					$lyric->lang = $song->lang;
					$lyric->time = $lyr['time'];
					$lyric->text = $lyr['text'];
					$lyric->save();
				}
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("songs/edit/{$song->id}"));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(duplicateRecord $error){
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
