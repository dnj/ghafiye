<?php
namespace packages\ghafiye\views\contribute;
use packages\ghafiye\{view as extendsClass, Contribute};

class View extends extendsClass {
	public function setContribute(Contribute $contibute) {
		$this->setData($contibute, "contibute");
	}
	protected function getContribute() {
		return $this->getData("contibute");
	}
}
