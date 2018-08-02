<?php
namespace packages\ghafiye\contributes\songs;
use packages\ghafiye\Contributes;

class Translate extends Contributes {
	protected $point = 10;
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
}