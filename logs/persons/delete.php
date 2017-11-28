<?php
namespace packages\ghafiye\logs\persons;
use \packages\base\view;
use \packages\userpanel\logs;
class delete extends logs{
	public function getColor():string{
		return "circle-bricky";
	}
	public function getIcon():string{
		return "fa fa-user-times";
	}
	public function buildFrontend(view $view){}
}
