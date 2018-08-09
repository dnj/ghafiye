<?php
namespace themes\clipone\views\resetpwd;
use \packages\userpanel\authentication;
use \packages\userpanel\views\resetpwd\newpwd as newpwdView;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
class newpwd extends newpwdView{
	use viewTrait, formTrait;
	protected $user;
	function __beforeLoad(){
		$this->user = authentication::getUser();
		$this->setTitle("تنظیم کلمه عبور جدید");
		$this->addBodyClass('newpwd');
	}
}
