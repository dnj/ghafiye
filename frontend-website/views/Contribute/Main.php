<?php
namespace themes\musixmatch\views\contribute;
use packages\base\{translator, options};
use themes\musixmatch\viewTrait;
use packages\ghafiye\{views\contribute\Main as parentView, contributes\songs};
class Main extends parentView {
	use viewTrait;
	protected $user;
	protected $users;
	protected $translatePoint;
	protected $syncdPoint;
	public function __beforeLoad(){
		$this->user = $this->getUser();
		$this->setTitle(translator::trans("ghafiye.contribute"));
		$this->addBodyClass("article");
		$this->addBodyClass("contribute");
		$this->addBodyClass("contribute-main");
		$this->users = $this->getWeeklyUsersLeaderboard();
		$this->translatePoint = (new songs\Translate)->getPoint();
		$this->syncdPoint = (new songs\Sync)->getPoint();
	}
}
