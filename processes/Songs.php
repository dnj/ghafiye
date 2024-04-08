<?php
namespace packages\ghafiye\processes;
use packages\base\{db, log, process, response};
use packages\ghafiye\song;

class Songs extends process {
	public function calculateTranslatesProgress(): response {
		$log = log::getInstance();
		$song = new song();
		$song->where("lang", "fa", "!=");
		foreach ($song->get() as $song) {
			$log->info("get count original lyrics for song ", $song->id);
			$lyric = new song\lyric();
			$lyric->where("song", $song->id);
			$lyric->where("lang", $song->lang);
			$lyric->where("status", song\lyric::published);
			$countOrginalLyrics = $lyric->count();
			if (!$countOrginalLyrics) {
				$log->reply()->error("cant to find lyrics");
				continue;
			} else {
				$log->reply($countOrginalLyrics, " lyrics found");
			}
			$lyric = new song\lyric();
			$lyric->where("song", $song->id);
			$lyric->where("lang", $song->lang, "!=");
			$lyric->groupBy("lang");
			$lyric->where("status", song\lyric::published);
			$lyrics = $lyric->get(null, array(
				"lang",
				"COUNT(lang) as `langs`",
			));
			foreach ($lyrics as $lyric) {
				$log->info("calculate translate progress for lang", $lyric->lang);
				$progress = ceil(($lyric->langs * 100) / $countOrginalLyrics);
				$log->reply($progress);
				$translate = new song\Translate();
				$translate->song = $song->id;
				$translate->lang = $lyric->lang;
				$translate->progress = $progress > 100 ? 100 : $progress;
				$translate->save();
			}
		}
		return new response(true);
	}
	public function ordering(): response {
		log::setLevel("info");
		$log = log::getInstance();
		$lyric = new song\lyric();
		$song = new song();
		$song->where("status", song::publish);
		$songs = $song->get();
		$log->info(count($songs), " song found");
		foreach ($songs as $song) {
			$log->info("try get original lyrics for song", $song->id);
			$lyric = new song\lyric();
			$lyric->where("song", $song->id);
			$lyric->where("parent", null, "is");
			$lyric->where("status", song\lyric::published);
			$lyrics = $lyric->get();
			$log->info(count($lyrics), " lyric found");
			$i = 1;
			foreach ($lyrics as $lyric) {
				$lyric->ordering = $i++;
				$lyric->save();
			}
		}
		return new response(true);
	}
	public function checkSyncedSongs(): response {
		log::setLevel("info");
		$log = log::getInstance();
		$lyric = new song\lyric();
		$song = new song();
		$song->where("status", song::publish);
		$songs = $song->get();
		$log->info(count($songs), " song found");
		foreach ($songs as $song) {
			$log->info("try get original lyrics for song", $song->id);
			$lyric = new song\lyric();
			$lyric->where("song", $song->id);
			$lyric->where("parent", null, "is");
			$lyric->where("status", song\lyric::published);
			$lyric->orderBy("ordering", "ASC");
			$lyrics = $lyric->get();
			$countLyrics = count($lyrics);
			$log->info($countLyrics, " lyric found");
			$countUnSyncedLyrics = 0;
			foreach ($lyrics as $key => $lyric) {
				if ($lyric->time == $key + 1) {
					$countUnSyncedLyrics++;
				}
			}
			$song->synced = ($countLyrics != $countUnSyncedLyrics) ? song::synced : 0;
			$song->save();
		}
		return new response(true);
	}
}
