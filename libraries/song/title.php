<?php
namespace packages\ghafiye\song;
use packages\base\db\dbObject;
use packages\ghafiye\person;
class title extends dbObject{
	const published = 1;
	const draft = 2;
	protected $dbTable = "ghafiye_songs_titles";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'song' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'title' => array('type' => 'text', 'required' => true),
		"status" => array("type" => "int", "required" => true),
	);
	protected function preLoad(array $data): array {
		if (!isset($data["status"])) {
			$data["status"] = self::draft;
		}
		return $data;
	}
}
