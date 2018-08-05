<?php
namespace packages\ghafiye\album;
use packages\base\db\dbObject;
class title extends dbObject{
	const published = 1;
	const draft = 2;
	protected $dbTable = "ghafiye_albums_titles";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'album' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'title' => array('type' => 'text', 'required' => true),
		"status" => array("type" => "int", "required" => true)
	);
	public static function byTitle(string $title){
		$obj = new static();
		$obj->where("title", $title);
		$obj->where("status", self::published);
		return $obj->getOne();
	}
}
