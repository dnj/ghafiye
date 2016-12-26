<?php
namespace packages\ghafiye\person;
use packages\base\db\dbObject;
class name extends dbObject{
	protected $dbTable = "ghafiye_persons_names";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'person' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'name' => array('type' => 'text', 'required' => true, 'unique' => true)
	);
	static function byName($name){
		$obj = new name();
		$obj->where("name", $name);
		return $obj->getOne();
	}
}
