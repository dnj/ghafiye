<?php
namespace packages\ghafiye\song;
use packages\base\db\dbObject;
class video extends dbObject{
	const published = 1;
	protected $dbTable = "ghafiye_songs_videos";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'song' => array('type' => 'int', 'required' => true),
		'title' => array('type' => 'text', 'required' => true),
		'identifier' => array('type' => 'text', 'required' => true, 'unquie' => true),
		'length' => array('type' => 'int', 'required' => true),
		'url' => array('type' => 'text', 'required' => true),
		'thumbnail' => array('type' => 'text', 'required' => true),
		'status' => array('type' => 'text', 'required' => true)
	);
}
