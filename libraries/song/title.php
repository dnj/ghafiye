<?php
namespace packages\ghafiye\song;
use packages\base\db\dbObject;
use packages\ghafiye\person;
class title extends dbObject{
	protected $dbTable = "ghafiye_songs_titles";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'song' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'title' => array('type' => 'text', 'required' => true)
	);
}
