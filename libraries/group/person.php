<?php
namespace packages\ghafiye\group;
use packages\base\db\dbObject;
class person extends dbObject{
	protected $dbTable = "ghafiye_groups_persons";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'group_id' => array('type' => 'int', 'required' => true),
		'person' => array('type' => 'int', 'required' => true)
	);
	protected $relations = array(
		'group' => array("hasOne", "packages\\ghafiye\\group", "group_id"),
		'person' => array("hasOne", "packages\\ghafiye\\person", "person")
    );
}
