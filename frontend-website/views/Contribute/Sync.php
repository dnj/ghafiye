<?php
namespace themes\musixmatch\views\contribute;
use packages\base\{translator, options};
use themes\musixmatch\{viewTrait, views\listTrait};
use packages\ghafiye\{views\contribute\Sync as parentView, contributes\songs};
class Sync extends parentView {
	use viewTrait, listTrait;
	protected $user;
	protected $users;
	protected $syncdPoint;
	public function __beforeLoad(){
		$this->user = $this->getUser();
		$this->setTitle(translator::trans("ghafiye.contribute"));
		$this->addBodyClass("article");
		$this->addBodyClass("contribute");
		$this->addBodyClass("contribute-sync");
		$this->users = $this->getWeeklyUsersLeaderboard();
		$this->syncdPoint = (new songs\Sync)->getPoint();
	}
}
