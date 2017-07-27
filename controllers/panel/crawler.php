<?php
namespace packages\ghafiye\controllers\panel;

use \packages\base\process;
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
use \packages\musixmatch\NoSizeSelectedException;
use \packages\musixmatch\image;

use \packages\ghafiye\person;
use \packages\ghafiye\song;
use \packages\ghafiye\album;
use \packages\ghafiye\crawler\queue;
use \packages\ghafiye\authorization;
use \packages\ghafiye\views\listview;
use \packages\ghafiye\processes\crawler as CrawlerProcess;

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
		$view = new listview();
		$this->items_per_page =  16;
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
					$this->searchArtists($inputs, $view);
					break;
				case(queue::track):
					$this->searchTrack($inputs, $view);
					break;
				case(queue::album):
					$this->searchAlbum($inputs, $view);
					break;
			}
			$this->response->setStatus(true);
		}catch(inputValidation $error){
			$this->response->setFormError(FormError::fromException($error));;
			$this->response->setStatus(false);
		}
		$this->response->setView($view);
		return $this->response;
	}
	private function getAPI(){
		if(!$this->musixmatch){
			$this->musixmatch = new musixmatch();
		}
		return $this->musixmatch;
	}
	private function downloadImage($image):string{
		static $package;
		if(!$package){
			$package = packages::package('ghafiye');
		}
		$path = 'storage/public/default-image.png';
		if($image instanceof image){
			$storage = new directory\local($package->getFilePath('storage/public/musixmatch'));
			if(!$storage->exists()){
				$storage->make(true);
			}
			try{
				$image->size(array([350,350], [500,500], [250,250], [100,100]));
				$file = $storage->file(md5('musixmatch-'.$image->id.'-'.$image->selectedSize['width'].'x'.$image->selectedSize['height']).'.'.substr($image->selectedSize['url'], strrpos($image->selectedSize['url'], '.')+1));
				if(!$file->exists()){
					$image->storeAs($file);
				}
				$path = 'storage/public/musixmatch/'.$file->basename;
			}catch(NoSizeSelectedException $e){

			}
		}
		return $package->url($path);
	}
	private function searchArtists(array $inputs, listview $view){
		$outArtists = [];
		$artists = $this->getAPI()
					->artist()
					->searchByName($inputs['name'])
					->orderBy('rate', 'desc');
		foreach($artists->paginate($this->page, $this->items_per_page) as $artist){
			$isExist = person::where("musixmatch_id", $artist->id)->has();
			$isQueued = queue::where("type", queue::artist)->where("MMID", $artist->id)->has();
			$outArtists[] = array(
				'id' => $artist->id,
				'rating' => $artist->rating,
				'name' => $artist->name,
				'country' => $artist->country,
				'avatar' => $this->downloadImage($artist->image),
				'isQueued' => $isQueued,
				'isExist' => $isExist,
			);
		}
		$view->setPaginate($this->page, $artists->totalCount, $this->items_per_page);
		$view->setDataList($outArtists);
	}
	private function searchTrack(array $inputs, listview $view){
		$tracks = [];
		$outTracks = [];
		if(isset($inputs['artist'])){
			$tracks = $this->searchTrackByArtist($inputs['artist']);
		}elseif(isset($inputs['album'])){
			$tracks = $this->searchTrackByAlbum($inputs['album']);
		}elseif(isset($inputs['name'])){
			$tracks =  $this->searchTrackByName($inputs['name']);
		}else{
			throw new \Exception("artist or name or album should passed");
		}
		$tracks->orderBy('rate', 'desc');
		foreach($tracks->paginate($this->page, $this->items_per_page) as $track){
			$isExist = song::where("musixmatch_id", $track->id)->has();
			$isQueued = queue::where("type", queue::track)->where("MMID", $track->id)->has();
			$outTrack = array(
				'id' => $track->id,
				'name' => $track->name,
				'length' => $track->length,
				'album_id' => $track->album_id,
				'album_name' => $track->album_name,
				'artist_name' => $track->artist_name,
				'genres' => [],
				'isQueued' => $isQueued,
				'isExist' => $isExist,
				'image' => $this->downloadImage($track->album_cover),
				'rating' => $track->rating
			);
			foreach($track->genres as $genre){
				$outTrack['genres'][] = array(
					'id' => $genre->id,
					'name' => $genre->fullName
				);
			}
			$outTracks[] = $outTrack;
		}
		$view->setPaginate($this->page, $tracks->totalCount, $this->items_per_page);
		$view->setDataList($outTracks);
	}
	private function searchTrackByArtist(int $artist){
		return $this->getAPI()->track()->searchByArtist($artist);
	}
	private function searchTrackByAlbum(int $album){
		return $this->getAPI()->track()->searchByAlbum($album);
	}
	private function searchTrackByName(string $name){
		return $this->getAPI()->track()->searchByName($name);
	}
	public function searchAlbum(array $inputs, listview $view){
		$outAlbums = [];
		if(!isset($inputs['artist'])){
			throw new \Exception("artist should passed");
		}
		$albums = $this->getAPI()
					->album()
					->searchByArtist($inputs['artist'])
					->orderBy('released_at', 'desc');
		foreach($albums->paginate($this->page, $this->items_per_page) as $album){
			$isExist = album::where("musixmatch_id", $album->id)->has();
			$isQueued = queue::where("type", queue::album)->where("MMID", $album->id)->has();
			$outAlbum = array(
				'id' => $album->id,
				'name' => $album->name,
				'length' => $album->length,
				'album_id' => $album->album_id,
				'album_name' => $album->album_name,
				'artist_name' => $album->artist_name,
				'genres' => [],
				'isQueued' => $isQueued,
				'isExist' => $isExist,
				'image' => $this->downloadImage($album->cover),
				'rating' => $album->rating
			);
			foreach($album->genres as $genre){
				$outAlbum['genres'][] = array(
					'id' => $genre->id,
					'name' => $genre->fullName
				);
			}
			$outAlbums[] = $outAlbum;
		}
		$view->setPaginate($this->page, $albums->totalCount, $this->items_per_page);
		$view->setDataList($outAlbums);
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
					'values' => [queue::artist, queue::album, queue::track]
				]
			];
			$inputs = $this->checkinputs($inputRules);
			$queue = new queue();
			$queue->MMID = $inputs['MMID'];
			$queue->type = $inputs['type'];
			$queue->status = queue::queued;
			$queue->save();

			$process = new process();
			$process->name = '\\'.CrawlerProcess::class.'@runJob';
			$process->parameters = array(
				'job' => $queue->id
			);
			$process->save();
			$process->runAndWaitFor(30);
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