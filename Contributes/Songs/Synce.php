<?php
namespace packages\ghafiye\contributes\songs;
use packages\ghafiye\{Contributes, contribute\Lyric, song, Contribute};

class Synce extends Contributes {
	protected $point = 5;
	public function buildFrontend(): string {
		return "";
	}
	public function getPoint(): int {
		return $this->point;
	}
	public function getImage(int $width, int $height): string {
		$this->contribute->song->getImage($width, $height);
	}
	public function getPreviewContent(): string {
		return "<p><a href=\"{$this->contribute->song->url()}\">{$this->contribute->song->title()}</a></p>
		<p><a href=\"" . base\url($this->contribute->getSinger()->encodedName()) . "\" class=\"song-singer\">{$this->contribute->getSinger()->name()}</a></p>";
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
		$this->contribute->status = Contribute::accepted;
		$this->contribute->save();
		$this->contribute->user->points += $this->point;
		$this->contribute->user->save();
	}
	public function onReject() {
		$this->contribute->status = Contribute::rejected;
		$this->contribute->save();
	}
}