<?php
namespace packages\ghafiye\musixmatch;
use \packages\base\db;
use \packages\base\json;
use \packages\ghafiye\jsonrpc;
use \packages\ghafiye\musixmatch\api;
use \packages\ghafiye\musixmatch\shareURLException;
use \packages\ghafiye\musixmatch\track\cover;
use \packages\ghafiye\musixmatch\track\translation;
use \packages\ghafiye\musixmatch\video;
use \packages\ghafiye\musixmatch\lyric;
use \packages\ghafiye\musixmatch\lyric\translation as lyric_translation;
use \packages\ghafiye\musixmatch\album;
use \packages\ghafiye\musixmatch\genre;
class track extends api{
	protected $dbTable = "ghafiye_musixmatch_tracks";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'id' => array('type' => 'int', 'required' => true),
		'spotifyId' => array('type' => 'text', 'unique' => true),
		'soundcloudId' => array('type' => 'text'),
		'name' => array('type' => 'text', 'required' => true),
		'rating' => array('type' => 'int'),
		'length' => array('type' => 'int'),
		'instrumental' => array('type' => 'bool'),
		'explicit' => array('type' => 'bool'),
		'hasLyrics' => array('type' => 'bool'),
		'hasSubtitles' => array('type' => 'bool'),
		'lyrics_id' => array('type' => 'int'),
		'subtitle_id' => array('type' => 'int'),
		'numFavourite' => array('type' => 'int'),
		'albumId' => array('type' => 'int'),
		'albumName' => array('type' => 'text'),
		'artistId' => array('type' => 'int'),
		'artistName' => array('type' => 'text'),
		'shareUrl' => array('type' => 'text'),
		'commontrackId' => array('type' => 'int'),
		'commontrackVanityId' => array('type' => 'text'),
		'restricted' => array('type' => 'bool'),
		'firstReleaseDate' => array('type' => 'text'),
		'updatedTime' => array('type' => 'text')
	);
	protected $relations = array(
		'lyric' => array('hasOne', 'packages\\ghafiye\\musixmatch\\lyric', 'lyrics_id')
	);
	public function crawl(){
		if($this->shareUrl){
			$rpc = new jsonrpc();
			if($rpc->connect("127.0.0.1", 8000)){
				$data = $rpc->request("musixmatchLoad", array("url" => $this->shareUrl."/translation/farsi"));
				if($data = json\decode($data)){
					return $data;
					if(isset($data['page']['lyrics']['lyrics']['body'])){
						return $data['page']['lyrics']['lyrics']['body'];
					}
				}
				$rpc->close();
			}
		}else{
			throw new shareURLException();
		}
	}
	public function save($data = null){
		if($this->spotifyId === ''){
			$this->spotifyId = null;
		}
		if($this->soundcloudId === 0){
			$this->soundcloudId = null;
		}
		if(!$this->isNew){
			if($this->id){
				parent::where($this->primaryKey, $this->id);
				$old_data = parent::has();
				if($old_data){
					//$this->original_data = $old_data->toArray();
					$this->isNew = false;
				}else{
					$this->isNew = true;
				}
			}else{
				$this->isNew = true;
			}
		}
		return parent::save($data);
	}
	public function getOne($fields = null){
		if($this->usingCache and $this->id){
			$this->where("id",$this->id);
			if($data = parent::getOne($fields)){
				return $data;
			}
		}
		if($this->usingCrawler){
			if($crawl = $this->crawl()){
				if(isset($crawl['page']['track'])){
					foreach(array_keys($this->dbFields) as $field){
						if(isset($crawl['page']['track'][$field])){
							$this->$field = $crawl['page']['track'][$field];
						}
					}
					$this->save();

					if(isset($crawl['lyrics']['translation']['list'])){
						foreach($crawl['lyrics']['translation']['list'] as $translationdata){
							$lang = '';
							if(isset($translationdata['languageFrom'])){
								$lang = $translationdata['languageFrom'];
							}elseif(isset($crawl['page']['lyrics']['lyrics']['language'])){
								$lang = $crawl['page']['lyrics']['lyrics']['language'];
							}
							$datatranslation = array(
								'id' => $translationdata['key'],
								'lyricsId' => $translationdata['lyricsId'],
								'commontrackId' => $translationdata['commontrackId'],
								'selectedLanguage' => $translationdata['selectedLanguage'],
								'languageFrom' => $lang,
								'description' => $translationdata['description'],
								'snippet' => $translationdata['snippet']
							);
							$translation = new lyric_translation($datatranslation);
							$translation->save();
						}
					}
					foreach(array_keys($crawl['page']['track']) as $key){
						if(substr($key, 0, strlen('albumCoverart')) == 'albumCoverart'){
							$size = substr($key, strlen('albumCoverart'));
							$cover = new cover();
							$cover->where('track', $this->id);
							$cover->where('size', $size);
							if($cover->getOne()){
								if($cover->image != $crawl['page']['track'][$key]){
									$cover->image = $crawl['page']['track'][$key];
									$cover->save();
								}
							}else{
								$cover = new cover();
								$cover->track = $this->id;
								$cover->size = $size;
								$cover->image = $crawl['page']['track'][$key];
								$cover->save();
							}
						}
					}
					foreach($crawl['page']['track']['lyricsTranslationStatus'] as $translationdata){
						$translation = new translation();
						$translation->where('track', $this->id);
						$translation->where('fromlang', $translationdata['from']);
						$translation->where('tolang', $translationdata['to']);
						if($translation->getOne()){
							if($translation->perc != $translationdata['perc'] * 100){
								$translation->perc = $translationdata['perc'] * 100;
								$translation->save();
							}
						}else{
							$translation = new translation();
							$translation->track = $this->id;
							$translation->fromlang = $translationdata['from'];
							$translation->tolang = $translationdata['to'];
							$translation->perc = $translationdata['perc'] * 100;
							$translation->save();
						}
					}
					if(isset($crawl['page']['track']['media']['videos']['list'])){
						foreach($crawl['page']['track']['media']['videos']['list'] as $videodata){
							$video = new video();
							if($video = $video->byId($videodata['id'])){
								foreach(array_keys($video->data) as $field){
									if(isset($videodata[$field])){
										$video->$field = $videodata[$field];
									}
								}
								$video->save();
							}else{
								$video = new video($videodata);
								$video->save();
							}
						}
					}
					if(isset($crawl['page']['lyrics']['lyrics'])){
						$lyricdata = $crawl['page']['lyrics']['lyrics'];
						if(isset($lyricdata['id'])){
							$lyric = new lyric();
							if($lyric = $lyric->byId($lyricdata['id'])){
								foreach(array_keys($lyric->data) as $field){
									if(isset($lyricdata[$field])){
										$lyric->$field = $lyricdata[$field];
									}
								}
								$lyric->save();
							}else{
								$lyric = new lyric($lyricdata);
								$lyric->trackId = $this->id;
								$lyric->save();
							}
						}
					}
					if(isset($crawl['page']['album'])){
						$album = new album();
						if($album = $album->byId($crawl['page']['album']['id'])){
							foreach(array_keys($album->data) as $field){
								if(isset($crawl['page']['album'][$field])){
									$album->$field = $crawl['page']['album'][$field];
								}
							}
							$album->save();
						}else{
							$album = new album($crawl['page']['album']);
							$album->save();
						}
					}
					//return $this;
				}
			}

		}
		$result = $this->sendRequest('track.get', array(
			'track_id' => $this->id
		));
		if(isset($result['track'])){
			//print_r($result['track']);
			$this->subtitle_id = $result['track']['subtitle_id'];
			$this->lyrics_id = $result['track']['lyrics_id'];
			$this->commontrackId = $result['track']['commontrack_id'];
			$genres = array();
			$music_genre_list = array_merge($result['track']['primary_genres']['music_genre_list'], $result['track']['secondary_genres']['music_genre_list']);

			foreach($music_genre_list as $genre){
				$genre = $genre['music_genre'];
				$genres[] = $genre['music_genre_id'];

				$genreObj = new genre();
				if($genreObj = $genreObj->byId($genre['music_genre_id'])){
					$genreObj->parent = $genre['music_genre_parent_id'];
					$genreObj->name = $genre['music_genre_name'];
					$genreObj->name_extended = $genre['music_genre_name_extended'];
					$genreObj->vanity = $genre['music_genre_vanity'];
					$genreObj->save();
				}else{
					$genreObj = new genre();
					$genreObj->id = $genre['music_genre_id'];
					$genreObj->parent = $genre['music_genre_parent_id'];
					$genreObj->name = $genre['music_genre_name'];
					$genreObj->name_extended = $genre['music_genre_name_extended'];
					$genreObj->vanity = $genre['music_genre_vanity'];
					$genreObj->save();
				}

				db::where("track", $this->id);
				db::where("genre", $genre['music_genre_id']);
				if(!db::has("ghafiye_musixmatch_tracks_genres")){
					db::insert("ghafiye_musixmatch_tracks_genres", array(
						'track' => $this->id,
						'genre' => $genre['music_genre_id']
					));
				}
			}
			if($genres){
				db::where("track", $this->id);
				db::where("genre", $genres, 'not in');
				db::delete('ghafiye_musixmatch_tracks_genres');
			}
			$this->save();
		}
		return $this;
	}
	public static function fromAPIData($data){
		$return = array();
		$pairs = array(
			'id' => 'track_id',
			'mbid' => 'track_mbid',
			'spotifyId' => 'track_spotify_id',
			'soundcloudId' => 'track_soundcloud_id',
			'name' => 'track_name',
			'rating' => 'track_rating',
			'length' => 'track_length',
			'instrumental' => 'instrumental',
			'explicit' => 'explicit',
			'hasLyrics' => 'has_lyrics',
			'hasSubtitles' => 'has_subtitles',
			'numFavourite' => 'num_favourite',
			'lyrics_id' => 'lyrics_id',
			'subtitle_id' => 'subtitle_id',
			'albumId' => 'album_id',
			'albumName' => 'album_name',
			'artistId' => 'artist_id',
			'artistName' => 'artist_name',
			'shareUrl' => 'track_share_url',
			'commontrackId' => 'commontrack_id',
			'commontrackVanityId' => 'commontrack_vanity_id',
			'restricted' => 'restricted',
			'updatedTime' => 'updated_time',
			'firstReleaseDate' => 'first_release_date'
		);
		foreach($pairs as $local => $remote){

			if(isset($data[$remote])){
				$val = $data[$remote];
				$return[$local] = $val !== '' ? $val : null;
			}
		}
		return $return;
	}
}
