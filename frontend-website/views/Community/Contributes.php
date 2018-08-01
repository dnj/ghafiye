<?php
namespace themes\musixmatch\views\community;
use \packages\base\{translator, options};
use \themes\musixmatch\viewTrait;
use \packages\ghafiye\views\community\Contributes as parentView;
class Contributes extends parentView {
	use viewTrait;
	protected $users;
	protected $userDefaultAvatar;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.community"));
		$this->addBodyClass("article");
		$this->addBodyClass("community");
		$this->addBodyClass("contributes");
		$this->users = $this->getWeeklyUsersLeaderboard();
		$this->userDefaultAvatar = options::get("packages.userpanel.default.avatar");
	}
}
