<?php
namespace packages\ghafiye\views\panel\contributes;
use packages\base\views\traits\form as formTrait;
use packages\ghafiye\{views\form, authorization, Contribute};

class View extends form {
	use formTrait;
	protected $canEdit;
	public function __construct() {
		$this->canEdit = authorization::is_accessed("contributes_edit");
	}
	public function setContribute(Contribute $contribute) {
		$this->setData($contribute, "contribute");
	}
	protected function getContribute() {
		return $this->getData("contribute");
	}
}
