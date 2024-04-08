<?php
namespace packages\ghafiye\listeners;
use packages\ghafiye\authentication;
use packages\base\frontend\events\throwDynamicData;
use packages\base\events\PackageRegistered;

class Base {
	public function beforeLoad(throwDynamicData $event){
		$event->setData("packages_ghafiye_isLogin", authentication::check());
	}
	public function packagesLoaded() {
		$userpanel = \packages\base\Packages::package('userpanel');
		if (!$userpanel) {
			return;
		}
		$reflection = new \ReflectionClass($userpanel);
		$property = $reflection->getProperty('frontends');
		$property->setAccessible(true);
		$property->setValue($userpanel, []);
	}
}
