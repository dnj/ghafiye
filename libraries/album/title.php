<?php
namespace packages\ghafiye\album;
use packages\base\db\dbObject;
class title extends dbObject{
	protected $dbTable = "ghafiye_albums_titles";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'album' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'title' => array('type' => 'text', 'required' => true)
	);
}
