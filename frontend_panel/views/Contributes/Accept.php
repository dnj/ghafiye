<?php
namespace themes\clipone\views\ghafiye\contributes;
use packages\base\translator;
use packages\ghafiye\{views\panel\contributes\Edit as parentView, Contribute, authorization};
use themes\clipone\{viewTrait, navigation, views\formTrait};

class Accept extends parentView {
	use viewTrait, formTrait;
	protected $contribute;
	protected $childrenTypes;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.panel.contributes.accept"));
		$this->contribute = $this->getContribute();
		$this->addBodyClass("contributes");
		$this->addBodyClass("contributes-accept");
		navigation::active("contributes");
		$this->childrenTypes = (int)authorization::childrenTypes();
	}
}
