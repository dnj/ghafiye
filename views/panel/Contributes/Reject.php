<?php
namespace packages\ghafiye\views\panel\contributes;
use packages\base\views\traits\form as formTrait;
use packages\ghafiye\{views\form, Contribute};

class Reject extends form {
	use formTrait;
	public function setContribute(Contribute $contribute) {
		$this->setData($contribute, "contribute");
	}
	protected function getContribute() {
		return $this->getData("contribute");
	}
}
