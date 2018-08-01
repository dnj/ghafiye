<?php
namespace packages\ghafiye\listeners;
use packages\ghafiye\authentication;
use packages\base\frontend\events\throwDynamicData;
class Base{
	public function beforeLoad(throwDynamicData $event){
		$event->setData("packages_ghafiye_isLogin", authentication::check());
	}
}
