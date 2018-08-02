<?php
namespace themes\musixmatch\views\contribute;
use packages\base\translator;
use themes\musixmatch\viewTrait;
use packages\ghafiye\views\contribute\View as parentView;

class View extends parentView {
	use viewTrait;
	protected $contribute;
	public function __beforeLoad(){
		$this->contribute = $this->getContribute();
		$this->setTitle(translator::trans("ghafiye.contribute.view"));
		$this->addBodyClass("article");
		$this->addBodyClass("contribute");
		$this->addBodyClass("contribute-view");
	}
}
