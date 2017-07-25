<?php
namespace packages\ghafiye\controllers\panel;

use \packages\base\packages;
use \packages\base\NotFound;
use \packages\base\translator;
use \packages\base\db\parenthesis;
use \packages\base\IO\file;
use \packages\base\IO\directory;

use \packages\userpanel;
use \packages\userpanel\view;
use \packages\userpanel\controller;

use \packages\musixmatch\api as musixmatch;

use \packages\ghafiye\person;
use \packages\ghafiye\crawler\queue;
use \packages\ghafiye\authorization;

class crawler extends controller{
	protected $authentication = true;
	private $musixmatch;
	public function queue(){
		authorization::haveOrFail('crawler_search');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\crawler\\search");
		$queue = new queue();
		$inputsRules = [
			'id' => [
				'type' => 'number',
				'optional' => true,
				'empty' => true
			],
			'type' => [
				'type' => 'number',
				'optional' => true,
				'empty' => true,
				'values' => queue::types
			],
			'MMID' => [
				'type' => 'number',
				'optional' => true,
				'empty' => true
			],
			'status' => [
				'type' => 'number',
				'optional' => true,
				'empty' => true,
				'values' => queue::statuses
			],
			'word' => [
				'type' => 'string',
				'optional' => true,
				'empty' => true
			],
			'comparison' => [
				'values' => ['equals', 'startswith', 'contains'],
				'default' => 'contains',
				'optional' => true
			]
		];
		try{
			$inputs = $this->checkinputs($inputsRules);
			foreach(array('id', 'type', 'MMID', 'status') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = 'equals';
					$queue->where($item, $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['word']) and $inputs['word']){
				if(!isset($inputs["MMID"]) or !$inputs["MMID"]){
					$parenthesis = new parenthesis();
					$parenthesis->where("MMID", $inputs['word'], $inputs['comparison']);
					$queue->where($parenthesis);
				}
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$queue->pageLimit = $this->items_per_page;
		$queues = $queue->paginate($this->page, "ghafiye_crawler_queue.*");
		$this->total_pages = $queue->totalPages;
		$view->setDataList($queues);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function add(){
		authorization::haveOrFail('crawler_add');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\crawler\\add");
		$this->response->setStatus(true);
		$inputRules = [
			'type' => [
				'type' => 'number',
				'values' => queue::types,
				'optional' => true
			],
			'lang' => [
				'type' => 'string',
				'values' => translator::$allowlangs,
				'optional' => true
			]
		];
		try{
			$inputs = $this->checkinputs($inputRules);
			if(isset($inputs['type'])){
				if($inputs['type']){
					if(!isset($inputs['type'])){

					}
				}else{
					unset($inputs['type']);
				}
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function search(){
		authorization::haveOrFail('crawler_add');
		$inputRules = [
			'type' => [
				'type' => 'number',
				'values' => queue::types
			],
			'name' => [
				'type' => 'string',
				'optional' => true,
			],
			'artist' => [
				'type' => 'number',
				'optional' => true,
			],
			'album' => [
				'type' => 'number',
				'optional' => true,
			],
			'track' => [
				'type' => 'number',
				'optional' => true,
			],
			'genre' => [
				'type' => 'number',
				'optional' => true,
			]
		];
		try{
			$inputs = $this->checkinputs($inputRules);
			$data = [];
			switch($inputs['type']){
				case(queue::artist):
					$this->response->setData($this->searchArtists($inputs), 'artists');
					break;
				case(queue::track):
					$this->searchByTrack($inputs);
					break;
				case(queue::album):
					$this->searchByAlbum($inputs);
					break;
			}
			$this->response->setData($inputs['type'], 'type');
			$this->response->setStatus(true);
		}catch(inputValidation $error){
			$this->response->setFormError(FormError::fromException($error));;
			$this->response->setStatus(false);
		}
		return $this->response;
	}
	private function getAPI(){
		if(!$this->musixmatch){
			$this->musixmatch = new musixmatch();
		}
		return $this->musixmatch;
	}
	private function searchArtists(array $inputs){
		$artists = [];
		$artistStorage = new directory\local(packages::package('ghafiye')->getFilePath('storage/public/artist/'));
		if(!$artistStorage->exists()){
			$artistStorage->make(true);
		}
		foreach($this->getAPI()->artist()->searchByName($inputs['name']) as $artist){
			$avatar = 'storage/public/default-image.png';
			if($artist->image and $artist->image){
				$file = $artistStorage->file(md5('musixmatch-'.$artist->image->id).'.jpg');
				if(!$file->exists()){
					$artist->image->size(array([350,350], [250,250]))->storeAs($file);
				}
				$avatar = 'storage/public/artist/'.$file->basename;
			}
			$isExist = person::where("musixmatch_id", $artist->id)->has();
			$isQueued = queue::where("type", queue::artist)->where("MMID", $artist->id)->has();
			$artists[] = array(
				'id' => $artist->id,
				'rating' => $artist->rating,
				'name' => $artist->name,
				'country' => $artist->country,
				'avatar' => packages::package('ghafiye')->url($avatar),
				'isQueued' => $isQueued,
				'isExist' => $isExist,
			);
		}
		return $artists;
	}
	private function searchByTrack(array $inputs){
		$tracks = [
			[
				"id" =>  113673904,
				"name" =>  "Let Me Love You",
				"length" =>  206,
				"album_id" =>  23853614,
				"album_name" =>  "Encore",
				"artist_name" => "DJ Snake feat. Justin Bieber",
				"genres" => [
					[
						"pop"
					]
				],
				'isQueued' => true,
				'isExist' => false,
				'image' => packages::package('ghafiye')->url('storage/public/default-image.png'),
				'rating' => rand(1, 100)
			],
			[
				"id" =>  113810442,
				"name" =>  "Cold Water",
				"length" =>  185,
				"album_id" =>  23866648,
				"album_name" =>  "Cold Water",
				"artist_name" => "DJ Snake feat. Justin Bieber",
				"genres" => [
					[
						"name" => "Electronic-Electronica",
						"id" => "1058"
					],
					[
						"name" => "Pop",
						"id" => 14
					]
				],
				'isQueued' => false,
				'isExist' => false,
				'image' => packages::package('ghafiye')->url('storage/public/default-image.png'),
				'rating' => rand(1, 100)
			],
			[
				"id" =>  84207136,
				"name" =>  "Love Yourself",
				"length" =>  234,
				"album_id" =>  20882700,
				"album_name" =>  "Purpose",
				"artist_name" => "DJ Snake feat. Justin Bieber",
				"genres" => [
					[
						"name" => "Pop",
						"id" => 14
					]
				],
				'isQueued' => false,
				'isExist' => true,
				'image' => packages::package('ghafiye')->url('storage/public/default-image.png'),
				'rating' => rand(1, 100)
			],
			[
				"id" =>  84207135,
				"name" =>  "Sorry",
				"length" =>  201,
				"album_id" =>  20882700,
				"album_name" =>  "Purpose",
				"artist_name" => "DJ Snake feat. Justin Bieber",
				"genres" => [
					[
						"name" => "Pop",
						"id" => 14
					]
				],
				'isQueued' => false,
				'isExist' => false,
				'image' => packages::package('ghafiye')->url('storage/public/default-image.png'),
				'rating' => rand(1, 100)
			]
		];
		$this->response->setData($tracks, 'tracks');
	}
	public function searchByAlbum(array $inputs){
		$albums = [
			[
				"id" => 11339785,
				"mbid" => null,
				"name" => "Atlas (From \"The Hunger Games => Catching Fire\" Soundtrack)",
				"rating" => 0,
				"track_count" => 1,
				"release_date" => "2013-09-06",
				"release_type" => "Single",
				"artist_id" => 1039,
				"artist_name" => "Coldplay",
				'image' => packages::package('ghafiye')->url('storage/public/default-image.png'),
				'isQueued' => true,
				'isExist' => false
			],
			[
				"id" => 11331552,
				"mbid" => "262de19d-4ed6-4f6f-aa7a-61dc50a34bce",
				"name" => "Atlas (From \"The Hunger Games => Catching Fire\")",
				"rating" => 100,
				"track_count" => 1,
				"release_date" => "2013-09-06",
				"release_type" => "Single",
				"artist_id" => 1039,
				"artist_name" => "Coldplay",
				'image' => packages::package('ghafiye')->url('storage/public/default-image.png'),
				'isQueued' => false,
				'isExist' => true
			],
			[
				"id" => 11339769,
				"mbid" => null,
				"name" => "Live 2012",
				"rating" => 97,
				"track_count" => 15,
				"release_date" => "2012-11-19",
				"release_type" => "Album",
				"artist_id" => 1039,
				"artist_name" => "Coldplay",
				'image' => packages::package('ghafiye')->url('storage/public/default-image.png'),
				'isQueued' => false,
				'isExist' => false
			],
			[
				"id" => 11306150,
				"mbid" => "6250e17a-57d8-4e4e-af7c-ba95e59078e9",
				"name" => "Charlie Brown (Jacques Lu Cont Remix)",
				"rating" => 63,
				"track_count" => 1,
				"release_date" => "2012-08-07",
				"release_type" => "Remix",
				"artist_id" => 1039,
				"artist_name" => "Coldplay",
				'image' => packages::package('ghafiye')->url('storage/public/default-image.png'),
				'isQueued' => false,
				'isExist' => false
			]
		];
		$this->response->setData($albums, 'albums');
	}
	public function store(){
		authorization::haveOrFail('crawler_add');
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\crawler\\add");
		$this->response->setStatus(true);
		try{
			$inputRules = [
				'MMID' => [
					'type' => 'number'
				],
				'type' => [
					'type' => 'number',
					'values' => [queue::artist ,queue::track]
				]
			];
			$inputs = $this->checkinputs($inputRules);
			$queue = new queue();
			$queue->MMID = $inputs['MMID'];
			$queue->type = $inputs['type'];
			$queue->status = queue::queued;
			$queue->save();
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));;
			$this->response->setStatus(false);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function delete(array $data){
		authorization::haveOrFail('crawler_delete');
		if(!$queue = queue::byId($data['queue'])){
			throw new NotFound();
		}
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\crawler\\delete");
		$view->setQueue($queue);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function terminate($data){
		authorization::haveOrFail('crawler_delete');
		if(!$queue = queue::byId($data['queue'])){
			throw new NotFound();
		}
		$view = view::byName("\\packages\\ghafiye\\views\\panel\\crawler\\delete");
		$this->response->setStatus(true);
		try{
			$queue->delete();
			$this->response->Go(userpanel\url('crawler/queue'));
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));;
			$this->response->setStatus(false);
		}
		$this->response->setView($view);
		return $this->response;
	}
}