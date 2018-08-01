<?php
namespace packages\ghafiye;
use \packages\base\http;
use \packages\base\db;
use \packages\base\response;
class controller extends \packages\base\controller{
	protected $page = 1;
	protected $total_pages = 1;
	protected $items_per_page = 25;
	function __construct(){
		parent::__construct();
		if(http::getURIData('page')){
			$this->page = http::getURIData('page');
			if($this->page < 1)$this->page = 1;
		}
		if(http::getURIData('ipp')){
			$this->items_per_page = http::getURIData('ipp');
			if($this->items_per_page < 1)$this->items_per_page = 1;
		}
		db::pageLimit($this->items_per_page);
	}
}
