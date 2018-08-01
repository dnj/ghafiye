<?php
namespace packages\ghafiye\contributes\songs;
use packages\ghafiye\Contributes;

class Synce extends Contributes {
	protected $point = 5;
	public function buildFrontend(): string {
		return "";
	}
	public function getPoint(): int {
		return $this->point;
	}
}