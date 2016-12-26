<?php
namespace packages\ghafiye\views\panel\group;
use \packages\ghafiye\group;
use \packages\ghafiye\views\form;
class delete extends form{
	public function setGroup(group $group){
		$this->setData($group, "group");
	}
	protected function getGroup(){
		return $this->getData("group");
	}
}
