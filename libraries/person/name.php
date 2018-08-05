<?php
namespace packages\ghafiye\person;
use packages\base\db\dbObject;
class name extends dbObject{
	const published = 1;
	const draft = 2;
	protected $dbTable = "ghafiye_persons_names";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'person' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'name' => array('type' => 'text', 'required' => true, 'unique' => true),
		"status" => array("type" => "int", "required" => true),
	);
	static function byName($name){
		$obj = new name();
		$obj->where("name", $name);
		$obj->where("status", self::published);
		return $obj->getOne();
	}
}
