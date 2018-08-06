<?php
namespace themes\clipone\views\ghafiye\contributes;
use packages\base\translator;
use packages\ghafiye\{views\panel\contributes\View as parentView, Contribute};
use themes\clipone\{viewTrait, navigation, views\formTrait};

class View extends parentView {
	use viewTrait, formTrait;
	protected $contribute;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.panel.contributes.view"));
		$this->contribute = $this->getContribute();
		$this->addBodyClass("contributes");
		$this->addBodyClass("contributes-view");
		navigation::active("contributes");
	}
}
