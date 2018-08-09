<?php
namespace themes\clipone;
use packages\base\{http, view\error, translator};
use packages\userpanel\{frontend, authorization, authentication};

trait viewTrait{
	public $metaTags = [];
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
	function getLogoHTML(){
		$logo = frontend::getLogoHTML();
		if(!$logo){
			$logo = 'CLIP<i class="clip-clip"></i>ONE';
		}
		return $logo;
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
	public function addMetaTag($options){
		if(!empty($options)){
			$this->metaTags[] = $options;
		}
	}
	public function buildMetaTags(){
		$this->addDefaultMetaTags();
		$html = '';
		foreach($this->metaTags as $options){
			$html .= "\t<meta";
			foreach($options as $key => $value){
				$html .= " {$key}=\"".htmlspecialchars($value)."\"";
			}
			$html .= ">\n";
		}
		echo $html;
	}
	public function addDefaultMetaTags(){
		$description = $this->getDescription();
		$properties = array_column($this->metaTags, 'property');
		$names = array_column($this->metaTags, 'name');
		if(!in_array('og:type', $properties)){
			$this->addMetaTag(array(
				'property' => 'og:type',
				'content' => 'website'
			));
		}
		if(!in_array('og:title', $properties)){
			$this->addMetaTag(array(
				'property' => 'og:title',
				'content' => $this->getTitle()
			));
		}
		if(!in_array('og:url', $properties)){
			$this->addMetaTag(array(
				'property' => 'og:url',
				'content' => http::getURL()
			));
		}
		if(!in_array('og:site_name', $properties)){
			$this->addMetaTag(array(
				'property' => 'og:site_name',
				'content' => translator::trans("jeyblog")
			));
		}
		if(!in_array('og:locale', $properties)){
			$this->addMetaTag(array(
				'property' => 'og:locale',
				'content' => translator::getCodeLang()
			));
		}
		if(!in_array('og:image', $properties)){
			$this->addMetaTag(array(
				'property' => 'og:image',
				'content' => theme::url('assets/images/favicon.ico', true)
			));
		}
		if(!in_array('og:description', $properties) and $description){
			$this->addMetaTag(array(
				'property' => 'og:description',
				'content' => $description
			));
		}
		if(!in_array('twitter:title', $names)){
			$this->addMetaTag(array(
				'name' => 'twitter:title',
				'content' => $this->getTitle()
			));
		}
		if(!in_array('twitter:card', $names)){
			$this->addMetaTag(array(
				'name' => 'twitter:card',
				'content' => 'summary'
			));
		}
		if(!in_array('twitter:description', $names) and $description){
			$this->addMetaTag(array(
				'name' => 'twitter:description',
				'content' => $description
			));
		}
		if(!in_array('twitter:url', $names)){
			$this->addMetaTag(array(
				'name' => 'twitter:url',
				'content' => http::getURL()
			));
		}
		if(!in_array('twitter:creator', $names)){
			$this->addMetaTag(array(
				'name' => 'twitter:creator',
				'content' => '@ghafiyecom'
			));
		}
		if(!in_array('twitter:site', $names)){
			$this->addMetaTag(array(
				'name' => 'twitter:site',
				'content' => '@ghafiyecom'
			));
		}
	}
	protected function canViewProfile(){
		return authorization::is_accessed('profile_view');
	}
	protected function getSelfAvatarURL(){
		$user = authentication::getUser();
		return $user->getAvatar(30, 30);
	}
}
