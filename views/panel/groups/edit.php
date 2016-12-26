<?php
namespace packages\ghafiye\views\panel\group;
use \packages\ghafiye\group;
use \packages\ghafiye\views\form;
use \packages\ghafiye\authorization;
class edit extends form{
	public function setGroup(group $group){
		$this->setData($group, "group");
		$this->setDataForm($group->avatar, "avatar");
		$this->setDataForm($group->lang, "group-lang");
	}
	protected function getGroup(){
		return $this->getData("group");
	}
}
