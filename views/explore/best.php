<?php
namespace packages\ghafiye\views\explore;
use \packages\ghafiye\views\listview;
use \packages\base\db\dbObject;
class best extends listView{
	public function setSongs($songs){
		$this->setDataList($songs);
	}
	public function getSongs(){
		return $this->getDataList();
	}
}
