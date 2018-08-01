<?php
namespace packages\ghafiye;

abstract class Contributes {
	protected $contribute;
	public function setContribute(Contribute $contribute) {
		$this->contribute = $contribute;
	}
	public abstract function buildFrontend(): string;
}