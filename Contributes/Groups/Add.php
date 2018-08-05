<?php
namespace packages\ghafiye\contributes\groups;
use packages\base;
use packages\ghafiye\Contributes;

class Add extends Contributes {
	protected $point = 10;
	public function getPoint():int {
		return $this->point;
	}
	public function getImage(int $width, int $height): string {
		return $this->contribute->group->getImage($width, $height);
	}
	public function getPreviewContent(): string {
		return "<a href=\"" . base\url($this->contribute->group->encodedName()) . "\">{$this->contribute->group->name($this->contribute->lang)}</a>";
	}
	public function buildFrontend(): string {
		$html = "";
		return $html;
	}
}