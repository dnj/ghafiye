<?php
namespace packages\ghafiye\group;
use packages\base\db\dbObject;
class title extends dbObject{
	protected $dbTable = "ghafiye_groups_titles";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'group_id' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'title' => array('type' => 'text', 'required' => true)
	);
}
