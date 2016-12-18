<?php
namespace packages\ghafiye\genre;
use packages\base\db\dbObject;
class title extends dbObject{
	protected $dbTable = "ghafiye_genres_titles";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'genre' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'title' => array('type' => 'text', 'required' => true)
	);
	static function byTitle($title){
		$obj = new self();
		$obj->where("title", $title);
		return $obj->getOne();
	}
}
