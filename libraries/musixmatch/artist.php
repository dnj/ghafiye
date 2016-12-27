<?php
namespace packages\ghafiye\musixmatch;
use \packages\ghafiye\musixmatch\api;
class artist extends api{
	public $id;
	public $mbid;
	public $vanity_id;
	public $name;
	public $comment;
	public $country;
	public $rating;
	public $primary_genres = array();
	public $secondary_genres = array();
	public $twitter;
	public $updated_time;
	public static function fromData($data){
		$artist = new self();
		$pairs = array(
			'id' => 'artist_id',
			'mbid' => 'artist_mbid',
			'vanity_id' => 'artist_vanity_id',
			'name' => 'artist_name',
			'comment' => 'artist_comment',
			'country' => 'artist_country',
			'rating' => 'artist_rating',
			'twitter' => 'artist_twitter_url',
			'updated_time' => 'updated_time'
		);
		foreach($pairs as $local => $remote){
			if(isset($data[$remote])){
				$val = $data[$remote];
				if($local == 'updated_time'){
					$val = strtotime($val);
				}
				$artist->$local = $val;
			}
		}
		return $artist;
	}
	public function get($paramters){
		if(!is_array($paramters)){
			$paramters = array(
				'artist' => $paramters
			);
		}
		$default_params = array(
			'artist' => array(
				'remote_key' => 'artist_id',
				'require' => true,
				'type' => 'string'
			)
		);
		$paramters = $this->buildParameters($default_params, $paramters);
		$result = $this->sendRequest('artist.get', $paramters);
		if(isset($result['artist'])){
			return artist::fromData($result['artist']);
		}
		return false;
	}
	public function search($paramters){
		$default_params = array(
			'artist' => array(
				'remote_key' => 'q_artist',
				'require' => true,
				'type' => 'string'
			),
			'artist_id' => array(
				'remote_key' => 'f_artist_id',
				'require' => false,
				'type' => 'string'
			),
			'page' => array(
				'require' => false,
				'type' => 'number'
			),
			'page_size' => array(
				'require' => false,
				'type' => 'number'
			)
		);
		$paramters = $this->buildParameters($default_params, $paramters);
		$result = $this->sendRequest('artist.search', $paramters);
		if(isset($result['artist_list'])){
			$response = array();
			foreach($result['artist_list'] as $artist_data){
				$response[] = artist::fromData($artist_data['artist']);
			}
			return $response;
		}
		return false;
	}
	public function albums($paramters = array()){
		if(!isset($paramters['artist']) and $this->id){
			$paramters['artist'] = $this->id;
		}
		$default_params = array(
			'artist' => array(
				'remote_key' => 'artist_id',
				'require' => true,
				'type' => 'string'
			),
			'release_date' => array(
				'remote_key' => 's_release_date',
				'require' => false,
				'type' => 'string'
			),
			'page' => array(
				'require' => false,
				'type' => 'number'
			),
			'page_size' => array(
				'require' => false,
				'type' => 'number'
			)
		);
		$paramters = $this->buildParameters($default_params, $paramters);
		$result = $this->sendRequest('artist.albums.get', $paramters);
		if(isset($result['album_list'])){
			$response = array();
			foreach($result['album_list'] as $album_data){
				$response[] = album::fromData($album_data['album']);
			}
			return $response;
		}
		return false;
	}
}
