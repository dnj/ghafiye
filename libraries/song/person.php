<?php
namespace packages\ghafiye\song;
use packages\base\db\dbObject;
class person extends dbObject{
	const singer = 1;
	const writer = 2;

	protected $dbTable = "ghafiye_songs_persons";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'song' => array('type' => 'int', 'required' => true),
		'person' => array('type' => 'int', 'required' => true),
		'role' => array('type' => 'int', 'required' => true),
		'primary' => array('type' => 'int', 'required' => true)
	);
}
