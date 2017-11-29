<?php
namespace packages\ghafiye\logs\genres;
use \packages\base\view;
use \packages\userpanel\logs;
class delete extends logs{
	public function getColor():string{
		return "circle-bricky";
	}
	public function getIcon():string{
		return "fa fa-times";
	}
	public function buildFrontend(view $view){}
}
