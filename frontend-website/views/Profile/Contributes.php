<?php
namespace themes\musixmatch\views\profile;
use \packages\base\translator;
use \themes\musixmatch\viewTrait;
use \packages\ghafiye\views\profile\Contributes as parentView;

class Contributes extends parentView {
	use viewTrait;
	protected $user;
	protected $contributes;
	function __beforeLoad(){
		$this->user = $this->getUser();
		$this->contributes = $this->getContributes();
		$this->setTitle(translator::trans("ghafiye.profile", ["title" => $this->user->getFullName()]));
		$this->addBodyClass("profile");
		$this->addBodyClass("contributes");
		$this->addBodyClass("article");
	}
}
