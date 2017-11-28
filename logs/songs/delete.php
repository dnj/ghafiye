<?php
namespace packages\ghafiye\logs\songs;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel;
use \packages\userpanel\{date, logs\panel, logs};
use \packages\ghafiye\{song, song\person};
class delete extends logs{
	public function getColor():string{
		return "circle-bricky";
	}
	public function getIcon():string{
		return "fa fa-music";
	}
	public function buildFrontend(view $view){}
}
