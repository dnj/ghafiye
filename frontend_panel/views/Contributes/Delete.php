<?php
namespace themes\clipone\views\ghafiye\contributes;
use packages\base\translator;
use packages\ghafiye\{views\panel\contributes\Delete as parentView, Contribute};
use themes\clipone\{viewTrait, navigation, views\formTrait};

class Delete extends parentView {
	use viewTrait, formTrait;
	protected $contribute;
	protected $childrenTypes;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.panel.contributes.delete"));
		$this->contribute = $this->getContribute();
		$this->addBodyClass("contributes");
		$this->addBodyClass("contributes-delete");
		navigation::active("contributes");
	}
}
