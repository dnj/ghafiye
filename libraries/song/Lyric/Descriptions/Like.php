<?php
namespace packages\ghafiye\song\lyric\description;
use packages\base\{db\dbObject, date};
use packages\ghafiye\{song\lyric, User};

class Like extends dbObject {
	protected $dbTable = "ghafiye_songs_lyrics_descriptions_likes";
	protected $primaryKey = "id";
	protected $dbFields = array(
		"description" => array("type" => "int", "required" => true),
		"ip" => array("type" => "text", "required" => true),
		"cookie" => array("type" => "text", "required" => true),
	);
    protected $relations = array(
        "description" => array("hasOne", description::class, "description"),
	);
}
