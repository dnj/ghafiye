<?php
namespace packages\ghafiye\contributes\songs;
use packages\base;
use packages\base\{translator, date};
use packages\ghafiye\{Contributes, contribute\Lyric, song, Contribute};

class Sync extends Contributes {
	protected $point = 25;
	protected $lyrics = array();
	public function getPoint(): int {
		return $this->point;
	}
	public function getImage(int $width, int $height): string {
		return $this->contribute->song->getImage($width, $height);
	}
	public function getPreviewContent(): string {
		return "<a href=\"{$this->contribute->song->url()}\">{$this->contribute->song->title($this->contribute->lang)}</a>
		<a class=\"song-singer\" href=\"" . base\url($this->contribute->song->getSinger()->encodedName()) . "\" class=\"song-singer\">{$this->contribute->song->getSinger()->name()}</a>";
	}
	public function buildFrontend(): string {
		$isLtr = $this->isLtr($this->contribute->song->lang);
		$html = '<div class="row">
				<div class="col-sm-4 col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<img src="' . $this->getImage(250, 250) . '" alt="' . $this->contribute->song->title() . '" class="img-responsive">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<label class="col-xs-5">' . translator::trans("ghafiye.song.title") . ':</label>
								<div class="col-xs-7' . ($isLtr ? " ltr" : "") .'">' . $this->contribute->song->title($this->contribute->song->lang) . '</div>
							</div>
							<div class="form-group">
								<label class="col-xs-5">' . translator::trans("ghafiye.singer") . ':</label>
								<div class="col-xs-7">' . $this->contribute->song->getSinger()->name() . '</div>
							</div>';
					if ($this->contribute->song->album) {
					$html .= '<div class="form-group">
								<label class="col-xs-5">' . translator::trans("ghafiye.album") . ':</label>
								<div class="col-xs-7' . ($this->isLtr($this->contribute->song->album->lang) ? " ltr" : "") .'">' . $this->contribute->song->album->title() . '</div>
							</div>';
					}
					if ($this->contribute->song->genre) {
					$html .= '<div class="form-group">
								<label class="col-xs-5">' . translator::trans("ghafiye.song.genre") . ':</label>
								<div class="col-xs-7">' . $this->contribute->song->genre->title() . '</div>
							</div>';
					}
				$html .= '</div>
					</div>
				</div>
				<div class="col-sm-8 col-xs-12">';
		foreach ($this->getSongLyrics() as $lyric) {
			$html .= '<div class="panel">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-2 col-xs-12">
									<p class="lyric-time">';
									$html .= $this->formatTime($lyric);
							$html .= '</p>
								</div>
								<div class="col-sm-10 col-xs-12">';
							$html .= '<p class="lyric'. ($isLtr ? " ltr" : "") . '">';
								$html .= $lyric->text;
							$html .= '</p>
								</div>
							</div>
						</div>
					</div>';
		}
		$html .= '</div>
			</div>';
		return $html;
	}
	public function onAccept() {
		$lyric = new Lyric();
		$lyric->where("contribute", $this->contribute->id);
		foreach ($lyric->get() as $lyr) {
			if ($lyr->lyric) {
				$lyr->lyric->time = $lyr->time;
				$lyr->lyric->save();
			}
		}
		$song = $this->contribute->song;
		$lyric = new song\lyric();
		$lyric->where("song", $song->id);
		$lyric->where("status", song\lyric::published);
		$synced = true;
		foreach ($lyric->get() as $lyric) {
			if (!$lyric->time) {
				$synced = false;
				break;
			}
		}
		if ($synced) {
			$song->update_at = date::time();
			$song->synced = song::synced;
			$song->save();
		}
		$this->contribute->status = Contribute::accepted;
		$this->contribute->save();
		$this->contribute->user->points += $this->point;
		$this->contribute->user->save();
	}
	public function onReject() {
		$this->contribute->status = Contribute::rejected;
		$this->contribute->save();
	}
	public function onDelete() {}
	protected function getSongLyrics(): array {
		$lyric = new song\lyric();
		$lyric->where("song", $this->contribute->song->id);
		$lyric->where("lang", $this->contribute->song->lang);
		$lyric->where("status", song\lyric::published);
		return $lyric->get();
	}
	protected function getContributeLyrics(): array {
		if (!$this->lyrics) {
			$lyric = new Lyric();
			$lyric->where("contribute", $this->contribute->id);
			$this->lyrics = $lyric->get();
		}
		return $this->lyrics;
	}
	protected function formatTime(song\lyric $lyric): string {
		foreach ($this->getContributeLyrics() as $lyr) {
			if (!$lyr->lyric) {
				continue;
			}
			if ($lyr->lyric->id == $lyric->id) {
				$lyric->time = $lyr->time;
				break;
			}
		}
		$min = floor($lyric->time / 60);
		$sec = $lyric->time % 60;
		return ($min < 10 ? "0" : "") . $min . ":" . ($sec < 10 ? "0" : "") . $sec;
	}
}