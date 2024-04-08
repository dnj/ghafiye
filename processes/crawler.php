<?php
namespace packages\ghafiye\processes;

use \Exception;

use \packages\base\db;
use \packages\base\process;
use \packages\base\log;
use \packages\base\packages;
use \packages\base\response;
use \packages\base\IO\file;
use \packages\base\IO\directory;

use \packages\musixmatch\api as musixmatch;
use \packages\musixmatch\NoSizeSelectedException;
use \packages\musixmatch\image;
use \packages\musixmatch\artist;
use \packages\musixmatch\album;
use \packages\musixmatch\track;
use \packages\musixmatch\genre;
use \packages\musixmatch\lyrics;

use \packages\ghafiye;
use \packages\ghafiye\person;
use \packages\ghafiye\song;
use \packages\ghafiye\crawler\queue;

class crawler extends process{
	private $musixmatch;

	public function start(){
		log::setLevel('debug');
		$log = log::getInstance();

		$log->info("looking for queued jobs");
		$jobs = (new queue)->where("status",queue::queued)->get();
		$log->reply(count($jobs), "found");

		foreach($jobs as $key => $queue){
			$log->info("run #{$queue->id}");
			$this->runJob($queue);
		}
	}
	public function runJob(array $data){
		log::setLevel('debug');
		if(!isset($data['job'])){
			throw new Exception("need job argument");
		}
		if(is_numeric($data['job'])){
			$data['job'] = (new queue)->byId($data['job']);
			return $this->runJob($data);
		}
		$log = log::getInstance();
		try{
			$log->debug("flag as running");
			$data['job']->status = queue::running;
			$data['job']->save();
			$log->debug("check for job type");
			switch($data['job']->type){
				case(queue::artist):
					$log->reply("Artist");
					$this->runArtistJob($data['job']);
					break;
				case(queue::track):
					$log->reply("Track");
					$this->runTrackJob($data['job']);
					break;
				case(queue::album):
					$log->reply("Album");
					$this->runAlbumJob($data['job']);
					break;
				default:
					$log->reply()->fatal("not supported (".$data['job']->type.")");
					break;
			}

			$log->debug("flag as passed");
			$data['job']->status = queue::passed;
			$data['job']->save();

		}catch(ItemAlreadyExistException $e){
			$log->debug("flag as failed");
			$data['job']->status = queue::faild;
			$data['job']->save();
		}
		return new response(true);
	}

	private function runArtistJob(queue $job){
		$log = log::getInstance();
		$log->info("get artist from musixmatch");
		$artist = $this->getAPI()->artist()->getByID($job->MMID);
		$log->info("import the artist as ghafiye person");
		$person = $this->importArtist($artist);
	}
	private function runTrackJob(queue $job){
		$log = log::getInstance();
		$log->info("get track from musixmatch");
		$track = $this->getAPI()->track()->getByID($job->MMID);
		$log->info("import the track as ghafiye song");
		$song = $this->importTrack($track);
	}
	private function runAlbumJob(queue $job){
		$log = log::getInstance();
		$log->info("get album from musixmatch");
		$album = $this->getAPI()->album()->getByID($job->MMID);
		$log->info("import the album as ghafiye album");
		$gAlbum = $this->importAlbum($album);
		$log->info("get the album's tracks");
		$tracks = $album->tracks()->all();
		foreach($tracks as $track){
			$log->info("import #{$track->id}");
			try{
				$this->importTrack($track);
			}catch(ItemAlreadyExistException $e){
				$log->reply("already exists");
			}
		}
	}

	private function getAPI(){
		if(!$this->musixmatch){
			$this->musixmatch = new musixmatch();
		}
		return $this->musixmatch;
	}
	private function downloadBigImage($image){
		static $package;
		if(!$package){
			$package = packages::package('ghafiye');
		}
		$path = null;
		if($image instanceof image){
			$log = log::getInstance();
			try{
				$image->size('big');

				$storage = new directory\local($package->getFilePath('storage/public/musixmatch'));
				if(!$storage->exists()){
					$log->debug("create musixmatch storage");
					$storage->make(true);
				}
				$file = $storage->file(md5('musixmatch-'.$image->id.'-'.$image->selectedSize['width'].'x'.$image->selectedSize['height']).'.'.substr($image->selectedSize['url'], strrpos($image->selectedSize['url'], '.')+1));
				if(!$file->exists()){
					$log->info("download big image");
					$log->debug("url = ", $image->selectedSize['url']);
					$image->storeAs($file);
					$log->reply("success");
				}
				$path = 'storage/public/musixmatch/'.$file->basename;
			}catch(NoSizeSelectedException $e){

			}
		}
		return $path;
	}
	private function downloadImage($image){
		static $package;
		if(!$package){
			$package = packages::package('ghafiye');
		}
		$path = null;
		if($image instanceof image){
			$log = log::getInstance();
			try{
				$image->size(array([350,350], [500,500], [250,250], [100,100]));

				$storage = new directory\local($package->getFilePath('storage/public/musixmatch'));
				if(!$storage->exists()){
					$log->debug("create musixmatch storage");
					$storage->make(true);
				}
				$file = $storage->file(md5('musixmatch-'.$image->id.'-'.$image->selectedSize['width'].'x'.$image->selectedSize['height']).'.'.substr($image->selectedSize['url'], strrpos($image->selectedSize['url'], '.')+1));
				if(!$file->exists()){
					$log->info("download image");
					$log->debug("url = ", $image->selectedSize['url']);
					$image->storeAs($file);
					$log->reply("success");
				}
				$path = 'storage/public/musixmatch/'.$file->basename;
			}catch(NoSizeSelectedException $e){

			}
		}
		return $path;
	}
	private function findPersonOrImport(int $artist):person{
		$log = log::getInstance();
		$log->debug("looking for artist");
		$person = (new person)->where("musixmatch_id", $artist)->getOne();
		if($person){
			$log->reply("fonud in ghafiye");
			return $person;
		}
		$log->reply("notfound");
		$log->debug("looking in musixmatch");
		$artist = $this->getAPI()->artist()->getByID($artist);
		$log->info("import the artist as ghafiye person");
		return $this->importArtist($artist);
	}
	private function findAlbumOrImport(int $album){
		$log = log::getInstance();
		$log->debug("looking for album");
		$gAlbum = (new ghafiye\album)->where("musixmatch_id", $album)->getOne();
		if($gAlbum){
			$log->reply("fonud in ghafiye");
			return $gAlbum;
		}
		$log->reply("notfound");
		$log->debug("looking in musixmatch");
		$album = $this->getAPI()->album()->getByID($album);
		if($album->track_count == 1){
			$log->debug("single albums doesn't need to import");
			return null;
		}
		$log->info("import the artist as ghafiye album");
		return $this->importAlbum($album);
	}

	private function findGenreOrImport(genre $genre){
		$log = log::getInstance();
		$log->debug("looking for genre");
		$gGenre = (new ghafiye\genre)->where("musixmatch_id", $genre->id)->getOne();
		if($gGenre){
			$log->reply("fonud in ghafiye");
			return $gGenre;
		}
		$log->reply("notfound");
		$log->info("import the genre as ghafiye genre");
		return $this->importGenre($genre);
	}

	private function importArtist(artist $artist):person{
		$log = log::getInstance();
		$log->info("check for existce of artist");
		if((new person)->where("musixmatch_id", $artist->id)->has()){
			$log->reply()->fatal("exists");
			throw new ItemAlreadyExistException();
		}
		$log->reply("notfound");
		$person = new person();
		$person->musixmatch_id = $artist->id;
		$person->rating = $artist->rating;
		$person->avatar = $this->downloadImage($artist->image);
		$person->cover = $this->downloadBigImage($artist->image);
		$person->save();
		$person->addName($artist->name, 'en');
		return $person;
	}
	private function importAlbum(album $album):ghafiye\album{
		$log = log::getInstance();
		$log->info("check for existce of album");
		if((new ghafiye\album)->where("musixmatch_id", $album->id)->has()){
			$log->reply()->fatal("exists");
			throw new ItemAlreadyExistException();
		}
		$log->reply("notfound");
		$gAlbum = new ghafiye\album();
		$gAlbum->musixmatch_id = $album->id;
		$gAlbum->image = $this->downloadImage($album->image);
		$gAlbum->lang = 'en';
		$gAlbum->save();
		$gAlbum->addTitle($album->name, 'en');
		return $gAlbum;
	}
	private function importGenre(genre $genre):ghafiye\genre{
		$log = log::getInstance();
		$log->info("check for existce of genre");
		if((new ghafiye\genre)->where("musixmatch_id", $genre->id)->has()){
			$log->reply()->fatal("exists");
			throw new ItemAlreadyExistException();
		}
		$log->reply("notfound");
		$gGenre = new ghafiye\genre();
		$gGenre->musixmatch_id = $genre->id;
		$gGenre->save();
		$gGenre->addTitle($genre->fullName, 'en');
		return $gGenre;
	}
	private function importTrack(track $track):song{
		$log = log::getInstance();
		$log->info("check for existce of track");
		if((new song)->where("musixmatch_id", $track->id)->has()){
			$log->reply()->fatal("exists");
			throw new ItemAlreadyExistException();
		}
		$log->reply("notfound");
		
		$log->info("looking for track's artist");
		$artist = $this->findPersonOrImport($track->artist_id);
		
		$log->info("looking for track's album");
		$album = $this->findAlbumOrImport($track->album_id);
		if($track->genres){
			$log->info("looking for track's genre");
			$genre = $this->findGenreOrImport($track->genres[0]);
		}else{
			$genre = null;
		}

		$lyrics = $track->hasSubtitle ? $track->subtitle() : $track->lyrics();

		$song = new song();
		$song->musixmatch_id = $track->id;
		$song->spotify_id = $track->spotify_id;
		$song->album = $album ? $album->id : null;
		$song->release_at = $track->released_at->getTimestamp();
		$song->update_at = $track->updated_at->getTimestamp();
		$song->duration = $track->length;
		$song->genre = $genre ? $genre->id : null;
		$song->lang = "fa";//$track->language ? $track->language : 'en';
		$song->image = $this->downloadImage($track->album_cover);
		$song->views = 0;
		$song->likes = intval($track->favourites);
		$song->status = song::draft;
		$song->save();
		$song->addTitle($track->name, $song->lang);

		$person = new song\person();
		$person->song = $song->id;
		$person->person = $artist->id;
		$person->role = song\person::singer;
		$person->primary = 1;
		$person->save();

		$this->importLyrics($lyrics, $track, $song);
		if($track->language != 'fa' and $track->hasTranslationTo('fa')){
			$this->importTranslatedLyrics($track, $song, 'fa');
		}
		return $song;
	}
	private function importLyrics(lyrics $lyrics, track $track, song $song){
		foreach($lyrics->texts as $key => $text){
			$lyric = new song\lyric;
			$lyric->song = $song->id;
			$lyric->lang = $lyrics->language;
			$lyric->time = $track->hasSubtitle ? $text['time'] : $key;
			$lyric->text = $text['text'];
			$lyric->save();
		}
	}
	private function importTranslatedLyrics(track $track, song $song, string $language){
		$lyrics = $track->translate($language);
		foreach($lyrics->texts as $text){
			if(!isset($text['translate'])){
				continue;
			}
			$lyric = new song\lyric;
			$lyric->where("song", $song->id);
			$lyric->where("parent", null, 'is');
			$lyric->where("text", $text['text']);
			foreach($lyric->get() as $parent){
				$lyric = new song\lyric;
				$lyric->song = $song->id;
				$lyric->lang = $language;
				$lyric->parent = $parent->id;
				$lyric->text = $text['translate'];
				$lyric->save();
			}
		}
	}
}
class ItemAlreadyExistException extends \Exception{

}
