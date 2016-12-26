<?php
namespace packages\ghafiye;
use \packages\userpanel\authorization as UserPanelAuthorization;
use \packages\userpanel\authentication;
class authorization extends UserPanelAuthorization{
	static function is_accessed($permission, $prefix = 'ghafiye'){
		return parent::is_accessed($permission, $prefix);
	}
	static function haveOrFail($permission, $prefix = 'ghafiye'){
		parent::haveOrFail($permission, $prefix);
	}
}
