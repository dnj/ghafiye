<?php
namespace themes\musixmatch\views\contribute;
use packages\base\{translator, options};
use themes\musixmatch\{viewTrait, views\listTrait};
use packages\ghafiye\{views\contribute\Synce as parentView, contributes\songs};
class Synce extends parentView {
	use viewTrait, listTrait;
	protected $user;
	protected $users;
	protected $syncedPoint;
	public function __beforeLoad(){
		$this->user = $this->getUser();
		$this->setTitle(translator::trans("ghafiye.contribute"));
		$this->addBodyClass("article");
		$this->addBodyClass("contribute");
		$this->addBodyClass("contribute-synce");
		$this->users = $this->getWeeklyUsersLeaderboard();
		$this->syncedPoint = (new songs\Synce)->getPoint();
	}
}
