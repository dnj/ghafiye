<?php
namespace packages\ghafiye;
use packages\base\db\dbObject;
use packages\ghafiye\translator\name;
class person extends dbObject{
	use name;
	protected $dbTable = "ghafiye_persons";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'user' => array('type' => 'int'),
		'name_prefix' => array('type' => 'text'),
		'first_name' => array('type' => 'text'),
		'middle_name' => array('type' => 'text'),
		'last_name' => array('type' => 'text'),
		'name_suffix' => array('type' => 'text'),
		'gender' => array('type' => 'int'),
		'avatar' => array('type' => 'text'),
		'cover' => array('type' => 'text')
	);
    protected $relations = array(
		'user' => array("hasOne", "packages\\userpanel\\user", "user"),
        'names' => array("hasMany", "packages\\ghafiye\\person\\name", "person")
    );
}
