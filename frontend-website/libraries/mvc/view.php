<?php
namespace themes\musixmatch;
use \packages\news\newpost;
trait viewTrait{
	protected $bodyClasses = array('rtl');
	function the_header($template = ''){
		require_once(__DIR__.'/../../html/header/'.($template ? $template : 'header').'.php');
	}
	function the_footer($template = ''){
		require_once(__DIR__.'/../../html/footer/'.($template ? $template : 'footer').'.php');
	}
	function the_sidebar($template){
		require_once(__DIR__.'/../../html/sidebar/'.($template ? $template : 'sidebar').'.php');
	}
	public function addBodyClass($class){
		$this->bodyClasses[] = $class;
	}
	public function removeBodyClass($class){
		if(($key = array_search($class, $this->bodyClasses)) !== false){
			unset($this->bodyClasses[$key]);
		}
	}
	protected function genBodyClasses(){
		return implode(' ', $this->bodyClasses);
	}
}
