<?php
namespace packages\ghafiye\processes;
use \packages\base\db;
use \packages\base\process;
use \packages\base\response;
use \packages\base\IO;
use \packages\base\packages;
use \packages\ghafiye\song;
use \packages\ghafiye\song\title;
use \packages\ghafiye\song\lyric;
use \packages\ghafiye\album;
use \packages\ghafiye\person;
use \packages\ghafiye\musixmatch\artist;
use \packages\ghafiye\musixmatch\album as MMalbum;
use \packages\ghafiye\musixmatch\track;
use \packages\ghafiye\musixmatch\track\cover;
use \packages\ghafiye\musixmatch\genre as MMgenre;
use \packages\ghafiye\musixmatch\lyric\translation as MMLTranslation;
use \packages\musixmatch\api;
use \packages\ghafiye\genre;

class musixmatch extends process{
	public function getAdele(){
		$api = new api();
		$track = $api->track()->getByID(78059644);
		print_r($track->translate("fa")->texts);
	}
	public function getAdeleAlbums(){
		$artist = $this->getAdele();
		print_r($artist->albums());
	}
	public function getTracks(){
		$album = new MMalbum(array(
			'id' => 11338198
		));
		$tracks = $album->getTracks();
		foreach($tracks as $track){
			db::where("id", $track->id);
			if(db::has("ghafiye_musixmatch_tracks")){
				$track->disableCache();
				$track->useCrawler();
				$track = $track->getOne();
				if($track->lyric){
					$this->addTrackToGhafiye($track);
				}else{
					//print_r($track);
				}
			}
		}
	}
	public function addTrackToGhafiye(track $track){


		$MMlyric = $track->lyric;

		$image = $this->getSongCover($track);
		$genre = $this->getSongGenre($track);
		$album = $this->getSongAlbum($track);



		$song = new song();
		$song->where("musixmatch_id", $track->id);
		if(!$song = $song->getOne()){
			$song = new song();
			$song->musixmatch_id = $track->id;
			$song->spotify_id = $track->spotifyId;
			$song->album = $album;
			$song->release_at = strtotime($track->firstReleaseDate);
			$song->duration = $track->length;
			$song->genre = $genre;
			$song->lang = $MMlyric->language;
			$song->likes = $track->numFavourite;
			$song->image = $image;
			$song->status = song::draft;
		}
		$song->save();
		$title = new title();
		$title->song = $song->id;
		$title->lang = $song->lang;
		$title->title = $track->name;
		$title->save();
		$this->addLyricsToSong($song, $track);
		$this->addArtistToSong($song, $track);
		//print_r($track->lyrics());
	}
	private function getArtist(track $track){
		$person = new person();
		$person->where("musixmatch_id", $track->artistId);
		if($person->getOne()){
			$person = $person->id;
		}else{
			$personOdj = new person();
			$person->musixmatch_id = $track->artistId;
			$person->save();
			$name = new person\name();
			$name->person = $person->id;
			$name->lang = 'en';
			$name->name = $track->artistName;
			$name->save();
			$person = $person->id;
		}
		return $person;
	}
	private function getSongCover(track $track){
		$image = null;
		$cover = new cover();
		$cover->where("track", $track->id);
		$cover->orderby("size", "desc");
		if($cover = $cover->getOne()){
			$tmpfile = packages::package('ghafiye')->getFilePath("storage/tmp/");
			if(!is_dir($tmpfile)){
				IO\mkdir($tmpfile, true);
			}
			$tmpfile .= time() * rand(0, 10);
			if(copy($cover->image, $tmpfile)){
				$ex = explode('.', $cover->image);
				$ex = $ex[count($ex) - 1];
				$md5 = IO\md5($tmpfile);
				$image = "storage/public/{$md5}.{$ex}";
				$pathimage = packages::package('ghafiye')->getFilePath($image);
				rename($tmpfile, $pathimage);
			}
		}
		return $image;
	}
	private function getSongGenre(track $track){
		db::where("track", $track->id);
		foreach(db::get("ghafiye_musixmatch_tracks_genres", null, "genre") as $MMgenre){
			$genre = new genre();
			$genre->where("musixmatch_id", $MMgenre['genre']);
			if($genre = $genre->getOne()){
				return $genre->id;
			}
		}
		return null;
	}
	private function getSongAlbum(track $track){
		$album = new album();
		$album->where("musixmatch_id", $track->albumId);
		if($album->getOne()){
			$album = $album->id;
		}else{
			$album = new album();
			$album->musixmatch_id = $track->albumId;
			$album->save();
			$title = new album\title();
			$title->album = $album->id;
			$title->lang = 'en';
			$title->title = $track->albumName;
			$title->save();
			$album = $album->id;
		}
		return $album;
	}
	private function addLyricsToSong(song $song, track $track){
		$MMlyric = $track->lyric;
		if($MMlyric){
			$lyricbody = explode("\n",$MMlyric->body);
			$x = 0;
			foreach($lyricbody as $line){
				$line = trim($line);
				if($line){
					$lyric = new lyric();
					$lyric->song = $song->id;
					$lyric->lang = $song->lang;
					$lyric->time = $x;
					$lyric->text = $line;
					$lyric->save();
					$translation = new MMLTranslation();
					$translation->where("commontrackId", $track->commontrackId);
					$translation->where("snippet", $lyric->text);
					foreach($translation->get() as $lyrictranslation){
						$translyric = new lyric();
						$translyric->song = $song->id;
						$translyric->lang = $lyrictranslation->selectedLanguage;
						$translyric->parent = $lyric->id;
						$translyric->time = $x;
						$translyric->text = $lyrictranslation->description;
						$translyric->save();
					}
					$x++;
				}
			}
		}
	}
	private function addArtistToSong(song $song, track $track){
		$artist = $this->getArtist($track);
		$person = new song\person();
		$person->song = $song->id;
		$person->person = $artist;
		$person->role = song\person::singer;
		$person->primary = true;
		$person->save();
	}
	public function getToken(){
		(new track)->generateAPICToken();
	}
}
