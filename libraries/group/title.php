<?php
namespace packages\ghafiye\group;
use packages\base\db\dbObject;
class title extends dbObject{
	const published = 1;
	const draft = 2;
	protected $dbTable = "ghafiye_groups_titles";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'group_id' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'title' => array('type' => 'text', 'required' => true),
		"status" => array("type" => "int", "required" => true),
	);
	public static function byTitle(string $title){
		$obj = new title();
		$obj->where('title', $title);
		$obj->where("status", self::published);
		return $obj->getOne();
	}
}
