<?php
namespace packages\ghafiye\song;
use packages\ghafiye\song;
use packages\base\db\dbObject;

class Translate extends dbObject{
	protected $dbTable = "ghafiye_songs_translates_progress";
	protected $primaryKey = "id";
	protected $dbFields = array(
		"song" => array("type" => "int", "required" => true),
		"lang" => array("type" => "text", "required" => true),
		"progress" => array("type" => "int", "required" => true),
	);
    protected $relations = array(
        "song" => array("hasOne", song::class, "song"),
    );
}
