<?php
namespace packages\ghafiye\views\panel\person;
use \packages\ghafiye\person;
use \packages\ghafiye\views\form;
use \packages\ghafiye\authorization;
class edit extends form{
	protected $canNameAdd;
	protected $canNameDel;
	function __construct(){
		$this->canNameAdd = authorization::is_accessed('person_name_add');
		$this->canNameDel = authorization::is_accessed('person_name_delete');
	}
	public function setPerson(person $person){
		$this->setData($person, "person");
		$this->setDataForm($person->musixmatch_id, "musixmatch_id");
		$this->setDataForm($person->name_prefix, "name_prefix");
		$this->setDataForm($person->first_name, "first_name");
		$this->setDataForm($person->middle_name, "middle_name");
		$this->setDataForm($person->last_name, "last_name");
		$this->setDataForm($person->name_suffix, "name_suffix");
		$this->setDataForm($person->gender, "gender");
		$this->setDataForm($person->avatar, "avatar");
		$this->setDataForm($person->cover, "cover");
	}
	protected function getPerson(){
		return $this->getData("person");
	}
}
