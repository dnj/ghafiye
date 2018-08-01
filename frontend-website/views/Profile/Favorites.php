<?php
namespace themes\musixmatch\views\profile;
use \packages\base\translator;
use \themes\musixmatch\viewTrait;
use \packages\ghafiye\views\profile\Favorites as parentView;

class Favorites extends parentView {
	use viewTrait;
	protected $user;
	protected $favorits;
	function __beforeLoad(){
		$this->user = $this->getUser();
		$this->favorites = $this->getFavoriteSongs();
		$this->setTitle(translator::trans("ghafiye.profile.favorits", ["title" => $this->user->getFullName()]));
		$this->addBodyClass("profile");
		$this->addBodyClass("favorites");
		$this->addBodyClass("article");
	}
}
