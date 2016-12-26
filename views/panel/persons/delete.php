<?php
namespace packages\ghafiye\views\panel\person;
use \packages\ghafiye\person;
use \packages\ghafiye\views\form;
class delete extends form{
	public function setPerson(person $person){
		$this->setData($person, "person");
	}
	public function getPerson(){
		return $this->getData("person");
	}
}
