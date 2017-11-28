<?php
namespace packages\ghafiye\logs\groups;
use \packages\base\view;
use \packages\userpanel\logs;
class add extends logs{
	public function getColor():string{
		return "circle-green";
	}
	public function getIcon():string{
		return "fa fa-users";
	}
	public function buildFrontend(view $view){}
}
