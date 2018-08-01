<?php
namespace packages\ghafiye\song;
use packages\base\db\dbObject;
use packages\ghafiye\{song, User};

class like extends dbObject{
	protected $dbTable = "ghafiye_songs_likes";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'cookie' => array('type' => 'text', 'required' => true),
		'ip' => array('type' => 'text', 'required' => true),
		'song' => array('type' => 'int', 'required' => true),
		"user" => array("type" => "int"),
	);
    protected $relations = array(
        "user" => array("hasOne", User::class, "user"),
        "song" => array("hasOne", song::class, "song"),
    );
}
