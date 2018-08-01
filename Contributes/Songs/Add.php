<?php
namespace packages\ghafiye\contributes\songs;
use packages\ghafiye\Contributes;

class add extends Contributes {
	protected $point = 10;
	public function buildFrontend(): string {
		return "";
	}
	public function getPoint():int {
		return $this->point;
	}
}