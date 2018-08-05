<?php
namespace packages\ghafiye\contributes\albums;
use packages\base;
use packages\ghafiye\Contributes;

class Add extends Contributes {
	protected $point = 10;
	public function getPoint():int {
		return $this->point;
	}
	public function getImage(int $width, int $height): string {
		return $this->contribute->album->getImage($width, $height);
	}
	public function getPreviewContent(): string {
		return "<a href=\"" . base\url($this->contribute->album->encodedName()) . "\">{$this->contribute->album->name($this->contribute->lang)}</a>";
	}
	public function buildFrontend(): string {
		$html = "";
		return $html;
	}
}