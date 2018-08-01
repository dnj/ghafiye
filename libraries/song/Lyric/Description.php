<?php
namespace packages\ghafiye\song\lyric;
use packages\base\{db\dbObject, date};
use packages\ghafiye\{song\lyric, User};

class Description extends dbObject {
	const accepted = 1;
	const waitForAccept = 2;
	const rejected = 3;
	protected $dbTable = "ghafiye_songs_lyrices_description";
	protected $primaryKey = "id";
	protected $dbFields = array(
		"lyric" => array("type" => "int", "required" => true),
		"user" => array("type" => "int", "required" => true),
		"text" => array("type" => "text", "required" => true),
		"sent_at" => array("type" => "int", "required" => true),
		"likes" => array("type" => "int"),
		"status" => array("type" => "int", "required" => true),
	);
    protected $relations = array(
        "lyric" => array("hasOne", lyric::class, "lyric"),
        "user" => array("hasOne", User::class, "user"),
	);
	public function preLoad(array $data): array {
		if (!isset($data["sent_at"])) {
			$data["sent_at"] = date::time();
		}
		if (!isset($data["status"])) {
			$data["status"] = self::waitForAccept;
		}
		return $data;
	}
}
