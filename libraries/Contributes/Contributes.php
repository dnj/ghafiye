<?php
namespace packages\ghafiye;

abstract class Contributes {
	protected $contribute;
	public function setContribute(Contribute $contribute) {
		$this->contribute = $contribute;
	}
	public abstract function buildFrontend(): string;
	public abstract function getPoint(): int;
	public abstract function getImage(int $width, int $height): string;
	public abstract function getPreviewContent(): string;
	protected function isLtr(string $lang): bool {
		return !in_array($lang, array("ar", "fa", "dv", "he", "ps", "sd", "ur", "yi", "ug", "ku"));
	}
}