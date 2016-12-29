<?php
namespace themes\musixmatch\views;
use \packages\base\http;
use \packages\base\translator;
trait listTrait{
	private $buttons;
	public function setButton($name, $active, $params = array()){
		if(!isset($params['classes'])){
			$params['classes'] = array('btn', 'btn-xs', 'btn-default');
		}
		if(isset($params['title']) and $params['title']){
			$params['classes'][] = 'tooltips';
		}
		if(!isset($params['link'])){
			$params['link'] = '#';
		}
		$button = array(
			'active' => $active,
			'params' => $params
		);
		$this->buttons[$name] = $button;
	}
	public function setButtonActive($name, $active){
		if(isset($this->buttons[$name])){
			$this->buttons[$name]['active'] = $active;
			return true;
		}
		return false;
	}
	public function setButtonParam($name, $parameter, $value){
		if(isset($this->buttons[$name])){
			$this->buttons[$name]['params'][$parameter] = $value;
			return true;
		}
		return false;
	}
	public function unsetButtonParam($name, $parameter){
		if(isset($this->buttons[$name])){
			unset($this->buttons[$name]['params'][$parameter]);
			return true;
		}
		return false;
	}
	public function hasButtons(){
		$have = false;
		foreach($this->buttons as $btn){
			if($btn['active']){
				$have = true;
				break;
			}
		}
		return $have;
	}
	public function genButtons($responsive = true){
		$buttons = array();
		foreach($this->buttons as $name => $btn){
			if($btn['active']){
				$buttons[$name] = $btn;
			}
		}
		$code = '';
		if($buttons){
			if($responsive and count($buttons) > 1){
				$code .= '<div class="visible-md visible-lg hidden-sm hidden-xs">';
			}
			foreach($buttons as $btn){
				$code .= '<a';
				if(isset($btn['params']['link']) and $btn['params']['link']){
					$code .= ' href="'.$btn['params']['link'].'"';
				}
				if(isset($btn['params']['classes']) and $btn['params']['classes']){
					if(is_array($btn['params']['classes'])){
						$btn['params']['classes'] = implode(" ", $btn['params']['classes']);
					}
					$code .= ' class="'.$btn['params']['classes'].'"';
				}
				if(isset($btn['params']['data']) and $btn['params']['data']){
					foreach($btn['params']['data'] as $name => $value){
						$code .= ' data-'.$name.'="'.$value.'"';
					}
				}
				if(isset($btn['params']['title']) and $btn['params']['title']){
					$code .= ' title="'.$btn['params']['title'].'"';
				}
				$code .= '>';
				if(isset($btn['params']['icon']) and $btn['params']['icon']){
					$code .= '<i class="'.$btn['params']['icon'].'"></i>';
				}
				$code .= '</a> ';
			}
			if($responsive and count($buttons) > 1){
				$code .= '</div>';
				$code .= '<div class="visible-xs visible-sm hidden-md hidden-lg">';
				$code .= '<div class="btn-group">';
				$code .= '<a class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown" href="#"><i class="fa fa-cog"></i> <span class="caret"></span></a>';
				$code .= '<ul role="menu" class="dropdown-menu pull-right">';
			}
			foreach($buttons as $btn){
				$code .= '<li><a tabindex="-1"';
				if(isset($btn['params']['link']) and $btn['params']['link']){
					$code .= ' href="'.$btn['params']['link'].'"';
				}
				if(isset($btn['data']) and $btn['params']['data']){
					foreach($btn['params']['data'] as $name => $value){
						$code .= ' data-'.$name.'="'.$value.'"';
					}
				}
				$code .= '>';
				if(isset($btn['params']['icon']) and $btn['params']['icon']){
					$code .= '<i class="'.$btn['params']['icon'].'"></i>';
				}
				if(isset($btn['params']['title']) and $btn['params']['title']){
					$code .= ' '.$btn['params']['title'];
				}
				$code .= '</a></li>';
			}
			if($responsive and count($buttons) > 1){
				$code .= '</ul></div></div>';
			}
		}


		return $code;
	}
	public function pager(){
		$prev_page = $this->currentPage-1;
        $next_page = $this->currentPage+1;

		$prev_active = ($this->currentPage > 1);
		$next_active = ($this->currentPage < $this->totalPages);

		$prev_link = $prev_active ? $this->pageurl($prev_page) : '#';
		$next_link = $next_active ? $this->pageurl($next_page) : '#';

		$prev_active = !$prev_active ? ' disabled' : '';
		$next_active = !$next_active ? ' disabled' : '';

		$return  = "<nav><ul class=\"pager\">";
		$return .= "<li class=\"previous{$prev_active}\"><a href=\"{$prev_link}\"><span aria-hidden=\"true\">&rarr;</span> ".translator::trans('explore.pager.older')."</a></li>";
		$return .= "<li class=\"next{$next_active}\"><a href=\"{$next_link}\">".translator::trans('explore.pager.newer')." <span aria-hidden=\"true\">&larr;</span></a></li>";
		$return .= "</ul></nav>";
		echo $return;
	}
	private function pageurl($page, $ipp = null){
		if($ipp === null){
			$ipp = $this->itemsPage;
		}
		if($ipp == 25){
			$ipp = null;
		}
		$paginationData = http::$request['get'];
		if($page != 1){
			$paginationData['page'] = $page;
		}else{
			unset($paginationData['page']);
		}
		if($ipp){
			$paginationData['ipp'] = $ipp;
		}else{
			unset($paginationData['ipp']);
		}
		return($paginationData ? '?'.http_build_query($paginationData) : http::$request['uri']);
	}
}
