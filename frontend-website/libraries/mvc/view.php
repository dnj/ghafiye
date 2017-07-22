<?php
namespace themes\musixmatch;
use \packages\base\view\error;
use \packages\base\translator;
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
	protected function getErrorsHTML(){
		$code = '';
		foreach($this->getErrors() as $error){
			$alert = array(
				'type' => 'info',
				'txt' => $error->getMessage(),
				'title' => ''
			);
			$data  = $error->getData();
			if(!is_array($data)){
				$data = array();
			}
			$alert = array_merge($alert, $data);
			if(!$alert['txt']){
				if($translator = translator::trans('error.'.$error->getCode())){
					$alert['txt'] = $translator;
				}else{
					$alert['txt'] = $error->getCode();
				}
			}
			switch($error->getType()){
				case(error::FATAL):
					$alert['type'] = 'danger';
					if(!$alert['title'])
						$alert['title'] = translator::trans('error.'.error::FATAL.'.title');
					break;
				case(error::WARNING):
					$alert['type'] = 'warning';
					if(!$alert['title'])
						$alert['title'] = translator::trans('error.'.error::WARNING.'.title');
					break;
				case(error::NOTICE):
					$alert['type'] = 'info';
					if(!$alert['title'])
						$alert['title'] = translator::trans('error.'.error::NOTICE.'.title');
					break;
			}
			if(isset($alert['classes']) and is_array($alert['classes'])){
				$alert['classes'] = implode(" ", $alert['classes']);
			}else{
				$alert['classes'] = '';
			}
			$code .= "<div class=\"alert alert-block alert-{$alert['type']} {$alert['classes']}\">";
			$code .= "<button data-dismiss=\"alert\" class=\"close\" type=\"button\">&times;</button>";
			$code .= "<h4 class=\"alert-heading\">";
			switch($alert['type']){
				case('danger'):$code.="<i class=\"fa fa-times-circle\"></i>";break;
				case('success'):$code.="<i class=\"fa fa-check-circle\"></i>";break;
				case('info'):$code.="<i class=\"fa fa-info-circle\"></i>";break;
				case('warning'):$code.="<i class=\"fa fa-exclamation-triangle\"></i>";break;
			}

			$code .= " {$alert['title']}</h4><p>{$alert['txt']}</p>";

			if(isset($alert['btns']) and $alert['btns']){
				$code .= "<p>";
				foreach($alert['btns'] as $btn){
					$code .= "<a href=\"{$btn['link']}\" class=\"btn {$btn['type']}\">{$btn['txt']}</a> ";
				}
				$code .= "</p>";
			}
			$code .= "</div>";
		}

		return $code;
	}
}
