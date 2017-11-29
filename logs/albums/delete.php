<?php
namespace packages\ghafiye\logs\albums;
use \packages\base\view;
use \packages\userpanel\logs;
class delete extends logs{
	public function getColor():string{
		return "circle-bricky";
	}
	public function getIcon():string{
		return "fa fa-file-audio-o";
	}
	public function buildFrontend(view $view){}
}
