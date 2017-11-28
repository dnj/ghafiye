<?php
namespace packages\ghafiye\logs\persons;
use \packages\base\view;
use \packages\userpanel\logs;
class add extends logs{
	public function getColor():string{
		return "circle-green";
	}
	public function getIcon():string{
		return "fa fa-user-plus";
	}
	public function buildFrontend(view $view){}
}
