<?php
namespace packages\ghafiye\controllers\panel;
use \packages\base\{IO, db, http, NotFound, packages, view\error, translator, db\parenthesis, inputValidation, views\FormError, db\duplicateRecord, dbObject};
use \packages\userpanel;
use \packages\userpanel\{controller, log, date};
use \packages\ghafiye\{view, song, album, group, genre, person, events, song\lyric, song\person as songPerson, authorization, authentication, views\panel\song as vSong, logs};

class songs extends controller{
	protected $authentication = true;
	public function listview(){
		authorization::haveOrFail('songs_list');
		$view = view::byName(vSong\listview::class);
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
		$view = view::byName(vSong\delete::class);
		$song = song::byId($data['id']);
		if(!$song)
			throw new NotFound();
		$view->setSong($song);
		$this->response->setStatus(false);
		if(http::is_post()){
			try{

				$log = new log();
				$log->user = authentication::getID();
				$log->title = translator::trans("ghafiye.logs.song.delete", ['song_id' => $song->id, 'song_title' => $song->title($song->lang)]);
				$log->type = logs\songs\delete::class;
				$log->parameters = ['song' => $song];
				$log->save();

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
		if(!$song = song::byId($data['song'])){
			throw new NotFound();
		}
		$view = view::byName(vSong\edit::class);
		$view->setSong($song);
		$allowlangs = translator::$allowlangs;
		$view->setAllowLangs($allowlangs);
		$view->setGenres(genre::get());
		if(http::is_post()){
	    	$this->response->setStatus(false);
			$inputsRules = array(
				'persons' => array(
					'optional' => true
				),
				'musixmatch_id' => array(
					'type' => 'string',
					'optional' => true,
					'empty' => true
				),
				'spotify_id' => array(
					'type' => 'string',
					'optional' => true,
					'empty' => true
				),
				'album' => array(
					'type' => 'string',
					'optional' => true,
					'empty' => true
				),
				'group' => array(
					'type' => 'string',
					'optional' => true,
					'empty' => true
				),
				'duration' => array(
					'type' => 'string',
					'optional' => true,
				),
				'genre' => array(
					'type' => 'string',
					'optional' => true
				),
				'lang' => array(
					'type' => 'string',
					'optional' => true,
					'values' => $allowlangs
				),
				'image' => array(
					'type' => 'file',
					'optional' => true,
					'empty' => true
				),
				'status' => array(
					'type' => 'number',
					'optional' => true,
					'values' => array(song::publish, song::draft)
				),
				'lyric_lang' => array(
					'type' => 'string',
					'optional' => true,
					'values' => $allowlangs
				),
				'titles' => array(
					'optional' => true
				),
				'lyric' => array(
					'optional' => true
				),
				"release_at" => [
					"type" => "date",
					"optional" => true,
				],
				"update_at" => [
					"type" => "date",
					"optional" => true,
				]
			);
			try{
				$lyricIDs = [];
				$inputs = $this->checkinputs($inputsRules);
				if (isset($inputs['release_at'])) {
					if ($inputs['release_at']) {
						$inputs['release_at'] = date::strtotime($inputs['release_at']);
					} else {
						unset($inputs['release_at']);
					}
				}
				if (isset($inputs['update_at'])) {
					if ($inputs['update_at']) {
						$inputs['update_at'] = date::strtotime($inputs['update_at']);
					} else {
						unset($inputs['update_at']);
					}
				}
				if(isset($inputs['group'])){
					if($inputs['group']){
						if(!group::byId($inputs['group'])){
							throw new inputValidation('group');
						}
					}else{
						unset($inputs['group']);
					}
				}
				if (isset($inputs['release_at'])) {
					if ($inputs['release_at'] < 0) {
						throw new inputValidation('release_at');
					}
				}
				if (isset($inputs['update_at'])) {
					if (isset($inputs['release_at'])) {
						if ($inputs['update_at'] < $inputs['release_at']) {
							throw new inputValidation('update_at');
						}
					}else{
						if ($inputs['update_at'] < $song->release_at) {
							throw new inputValidation('update_at');
						}
					}
					
				}
				if(isset($inputs['persons'])){
					if($inputs['persons']){
						if(is_array($inputs['persons'])){
							foreach($inputs['persons'] as $key => $person){
								if(!person::byId($person['id'])){
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
					}else{
						unset($inputs['persons']);
					}
				}
				if(!isset($inputs['persons']) and !isset($inputs['group'])){
					throw new unknowSongsArtistException();
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
						if(!album::byId($inputs['album'])){
							throw new inputValidation('album');
						}
					}else{
						unset($inputs['album']);
					}
				
				}
				if(isset($inputs['genre'])){
					if($inputs['genre']){
						if(!genre::byId($inputs['genre'])){
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
									$type_name = 'jpg';
									break;
								case(IMAGETYPE_GIF):
									$type_name = 'gif';
									break;
								case(IMAGETYPE_PNG):
									$type_name = 'png';
									break;
							}
							$directory = packages::package('ghafiye')->getFilePath("storage/public/songs/{$title}.{$type_name}");
							if(move_uploaded_file($inputs["image"]['tmp_name'], $directory)){
								$inputs["image"] = "storage/public/songs/{$title}.{$type_name}";
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

				$logsfields = [
					'musixmatch_id',
					'spotify_id',
					'album',
					'group',
					'duration',
					'lang',
					'status',
					"release_at",
					"update_at",
				];
				$parameters = [
					'oldData' => []
				];
				foreach ($logsfields as $field) {
					if (isset($inputs[$field])) {
						if (in_array($field, ["album", "group"])) {
							if ($song->$field->id != $inputs[$field]) {
								$parameters['oldData'][$field] = $song->$field;
							}
						} else if ($song->$field != $inputs[$field]) {
							$parameters['oldData'][$field] = $song->$field;
						}
					}
				}
				foreach(array('musixmatch_id', 'spotify_id', "update_at") as $key){
					if(isset($inputs[$key]) and $inputs[$key]){
						$song->$key = $inputs[$key];
					}else{
						$song->$key = null;
					}
				}
				foreach(['group', 'album'] as $item){
					if(isset($inputs[$item]) and $inputs[$item]){
						$song->$item = $inputs[$item];
					}else{
						$song->$item = null;
					}
				}
				foreach(array('lang', 'status', 'image', 'genre', 'duration', "release_at") as $key){
					if(isset($inputs[$key]) and $inputs[$key]){
						$song->$key = $inputs[$key];
					}
				}

				if(isset($inputs['titles'])){
					foreach($song->titles as $title){
						if(!isset($inputs['titles'][$title->lang])){
							$parameters['oldData']['titles'][] = $title;
							$title->delete();
						}else{
							if($title->title != $inputs['titles'][$title->lang]){
								$parameters['oldData']['titles'][] = $title;
								$title->title = $inputs['titles'][$title->lang];
							}
							$title->save();
						}
					}
					foreach($inputs['titles'] as $lang => $title){
						$song->setTitle($title, $lang);
					}
				}
				if(isset($inputs['persons'])){
					foreach($song->persons as $person){
						if(!isset($inputs['persons'][$person->person->id])){
							$parameters['oldData']['persons'][] = $person;
							$person->delete();
						}else{
							$person->primary = isset($inputs['persons'][$person->person->id]['primary']);
							if($person->role != $inputs['persons'][$person->person->id]['role']){
								$parameters['oldData']['persons'][] = $person;
								$person->role = $inputs['persons'][$person->person->id]['role'];
							}
							$person->save();
						}
					}
					foreach($inputs['persons'] as $key => $person){
						$songPerson = new songPerson();
						$songPerson->song = $song->id;
						$songPerson->person = $key;
						$songPerson->primary = isset($inputs['persons'][$key]['primary']);
						$songPerson->role = $inputs['persons'][$key]['role'];
						$songPerson->save();
					}
				}
				if(isset($inputs['lyric'])){
					foreach($song->getLyricByLang($inputs['lyric_lang']) as $lyric){
						if(!in_array($lyric->id, $lyricIDs)){
							$parameters['oldData']['lyrics'][] = $lyric;
							$lyric->delete();
						}
					}
					foreach($inputs['lyric'] as $lyric){
						if(isset($lyric['obj'])){
							if($lyric['obj']->parent){
								$lyric['obj']->parent = $lyric['parent'];
							}
							$time = $isOriginalLyric ? $lyric['time'] : 0;
							if($lyric['obj']->time != $time or $lyric['obj']->text != $lyric['text']){
								$parameters['oldData']['lyrics'][] = $lyric['obj'];
							}
							$lyric['obj']->time = $time;
							$lyric['obj']->text = $lyric['text'];
							$lyric['obj']->save();
						}else{
							if(isset($lyric['parent']) and $lyric['parent'] instanceof lyric){
								$lyric['obj'] = new lyric();
								$lyric['obj']->song = $song->id;
								$lyric['obj']->lang = $inputs['lyric_lang'];
								$lyric['obj']->parent = $lyric['parent']->id;
							}else{
								$lyric['obj'] = new lyric();
								$lyric['obj']->song = $song->id;
								$lyric['obj']->lang = $inputs['lyric_lang'];
							}
						}
					}
				}
				
				$song->save();

				$log = new log();
				$log->user = authentication::getID();
				$log->title = translator::trans("ghafiye.logs.song.edit", ['song_id' => $song->id, 'song_title' => $song->title($song->lang)]);
				$log->type = logs\songs\edit::class;
				$log->parameters = $parameters;
				$log->save();

				$this->response->setStatus(true);
			}catch(inputValidation $error){
				var_dump($error);
				$view->setFormError(FormError::fromException($error));
			}catch(unknowSongsArtistException $e){
				$error = new error();
				$error->setCode('ghafiye.panel.edit.song.unknowSongsArtistException');
				$error->setMessage(translator::trans('error.ghafiye.panel.edit.song.unknowSongsArtistException'));
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
		authorization::haveOrFail('song_add');
		$view = view::byName(vSong\add::class);
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
					'optional' => true,
					'empty' => true
				),
				'group' => array(
					'type' => 'number',
					'optional' => true,
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
				'persons' => array(
					'optional' => true
				),
				"release_at" => [
					"type" => "date",
					"optional" => true,
				]
			);
			try{
				$inputs = $this->checkinputs($inputsRules);
				if (isset($inputs["release_at"])) {
					if ($inputs["release_at"]) {
						$inputs["release_at"] = date::strtotime($inputs["release_at"]);
					} else {
						unset($inputs["release_at"]);
					}
				}
				if (isset($inputs["release_at"])) {
					if ($inputs["release_at"] <= 0) {
						throw new inputValidation('release_at');
					}
				}
				if(isset($inputs['group'])){
					if($inputs['group']){
						if(!group::byId($inputs['group'])){
							throw new inputValidation('group');
						}
					}else{
						unset($inputs['group']);
					}
				}
				if(isset($inputs['persons'])){
					if($inputs['persons']){
						if(is_array($inputs['persons'])){
							foreach($inputs['persons'] as $key => $person){
								if(!person::byId($person['id'])){
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
					}else{
						unset($inputs['persons']);
					}
				}
				if(!isset($inputs['persons']) and !isset($inputs['group'])){
					throw new unknowSongsArtistException();
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
				if(!genre::byId($inputs['genre'])){
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
				foreach(array('lang', 'status', 'genre', 'duration', "release_at") as $key){
					$song->$key = $inputs[$key];
				}
				foreach(array('musixmatch_id', 'spotify_id', 'group', 'album', 'image') as $key){
					if(isset($inputs[$key]) and $inputs[$key]){
						$song->$key = $inputs[$key];
					}
				}
				$song->save();
				$song->setTitle($inputs['title'], $inputs['lang']);
				if(isset($inputs['persons'])){
					foreach($inputs['persons'] as $key => $person){
						$songPerson = new songPerson();
						$songPerson->song = $song->id;
						$songPerson->person = $person['id'];
						$songPerson->primary = isset($person['primary']);
						$songPerson->role = $person['role'];
						$songPerson->save();
					}
				}
				foreach($inputs['lyric'] as $lyr){
					$lyric = new lyric();
					$lyric->song = $song->id;
					$lyric->lang = $song->lang;
					$lyric->time = $lyr['time'];
					$lyric->text = $lyr['text'];
					$lyric->save();
				}
				$log = new log();
				$log->user = authentication::getID();
				$log->title = translator::trans("ghafiye.logs.song.add", ['song_id' => $song->id, 'song_title' => $song->title($song->lang)]);
				$log->type = logs\songs\add::class;
				$log->save();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("songs/edit/{$song->id}"));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(unknowSongsArtistException $e){
				$error = new error();
				$error->setCode('ghafiye.panel.edit.song.unknowSongsArtistException');
				$error->setMessage(translator::trans('error.ghafiye.panel.edit.song.unknowSongsArtistException'));
				$view->addError($error);
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
}
class unknowSongsArtistException extends \Exception{}