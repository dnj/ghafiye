<?php
namespace packages\ghafiye\views\explore;
use \packages\ghafiye\view;
use \packages\base\db\dbObject;
class lastest extends view{
	public function setSongs($songs){
		$this->setData($songs, 'songs');
	}
	public function getSongs(){
		return $this->getData('songs');
	}
	public function export(){
		return array(
			'data' => array(
				'songs' => dbObject::objectToArray($this->getSongs())
			)
		);
	}
}
