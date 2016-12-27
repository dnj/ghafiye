<?php
namespace packages\ghafiye\musixmatch;
use \packages\ghafiye\musixmatch\api;
class album extends api{
	protected $dbTable = "ghafiye_musixmatch_albums";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'id' => array('type' => 'int', 'required' => true),
		'mbid' => array('type' => 'text', 'unique' => true),
		'name' => array('type' => 'text', 'required' => true),
		'rating' => array('type' => 'int'),
		'trackCount' => array('type' => 'int'),
		'releaseDate' => array('type' => 'text'),
		'releaseType' => array('type' => 'text'),
		'artistId' => array('type' => 'int'),
		'artistName' => array('type' => 'text'),
		'pline' => array('type' => 'text'),
		'copyright' => array('type' => 'text'),
		'label' => array('type' => 'text'),
		'vanityId' => array('type' => 'text', 'unique' => true),
		'restricted' => array('type' => 'bool'),
		'updatedTime' => array('type' => 'text')
	);
	public function save($data = null){
		if(!$this->isNew){
			if($this->id){
				parent::where($this->primaryKey, $this->id);
				$this->isNew = !parent::has();
			}else{
				$this->isNew = true;
			}
		}
		return parent::save($data);
	}


	public static function fromAPIData($data){
		$return = array();
		$pairs = array(
			'id' => 'album_id',
			'mbid' => 'album_mbid',
			'name' => 'album_name',
			'rating' => 'album_rating',
			'trackCount' => 'album_track_count',
			'releaseDate' => 'album_release_date',
			'releaseType' => 'album_release_type',
			'artistId' => 'artist_id',
			'artistName' => 'artist_name',
			'updatedTime' => 'updated_time',
			'coverart' => 'album_coverart_100x100'
		);
		foreach($pairs as $local => $remote){
			if(isset($data[$remote])){
				$val = $data[$remote];
				$return[$local] = $val;
			}
		}
		return $return;
	}
	public function getOne($fields = null){
		if($this->usingCache and $this->id){
			$this->where("id",$this->id);
			if($data = parent::getOne($fields)){
				return $data;
			}
		}
		$result = $this->sendRequest('album.get', array(
			'album_id' => $this->id
		));
		if(isset($result['album'])){
			foreach(self::fromAPIData($result['album']) as $key => $val){
				$this->$key = $val;
			}
			return $this;
		}
		return false;
	}
	public function getTracks(){
		$result = $this->sendRequest('album.tracks.get', array(
			'album_id' => $this->id
		));
		if(isset($result['track_list'])){
			$response = array();
			foreach($result['track_list'] as $track_data){
				$trackData = track::fromAPIData($track_data['track']);
				$track = new track($trackData);
				$track->save();
				$response[] = $track;
			}
			return $response;
		}
		return false;
	}
}
