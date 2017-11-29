<?php
namespace packages\ghafiye\logs\genres;
use \packages\base\view;
use \packages\userpanel\logs;
class add extends logs{
	public function getColor():string{
		return "circle-green";
	}
	public function getIcon():string{
		return "fa fa-plus";
	}
	public function buildFrontend(view $view){}
}
