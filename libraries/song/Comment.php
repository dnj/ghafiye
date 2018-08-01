<?php
namespace packages\ghafiye\song;
use packages\ghafiye\song;
use packages\base\db\dbObject;

class Comment extends dbObject {
	const accepted = 1;
	const waitForAccept = 2;
	const rejected = 3;
	protected $dbTable = "ghafiye_songs_comments";
	protected $primaryKey = "id";
	protected $dbFields = array(
		"song" => array("type" => "int", "required" => true),
		"reply" => array("type" => "int"),
		"sent_at" => array("type" => "int", "required" => true),
		"name" => array("type" => "text", "required" => true),
		"email" => array("type" => "text", "required" => true),
		"content" => array("type" => "text", "required" => true),
		"status" => array("type" => "int", "required" => true),
	);
    protected $relations = array(
        "song" => array("hasOne", song::class, "song"),
	);
	public function getReply() {
		if (!$this->reply) {
			return false;
		}
		$comment = new static();
		$comment->where("id", $this->reply);
		return $comment->getOne();
	}
}
