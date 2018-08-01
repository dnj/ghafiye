<?php
namespace themes\musixmatch\views\contribute;
use packages\base\{translator, options};
use themes\musixmatch\{viewTrait, views\listTrait};
use packages\ghafiye\{views\contribute\Translate as parentView, contributes\songs};
class Translate extends parentView {
	use viewTrait, listTrait;
	protected $user;
	protected $users;
	protected $translatePoint;
	public function __beforeLoad(){
		$this->user = $this->getUser();
		$this->setTitle(translator::trans("ghafiye.contribute"));
		$this->addBodyClass("article");
		$this->addBodyClass("contribute");
		$this->addBodyClass("contribute-translate");
		$this->users = $this->getWeeklyUsersLeaderboard();
		$this->translatePoint = (new songs\Translate)->getPoint();
	}
}
