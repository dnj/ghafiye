<?php
namespace packages\ghafiye;
use packages\userpanel\date;
use packages\base\{db, db\dbObject};

class Contribute extends dbObject {
	protected $handler;
	protected $dbTable = "ghafiye_contributes";
	protected $primaryKey = "id";
	protected $dbFields = array(
		"title" => array("type" => "text", "required" => true),
		"user" => array("type" => "int", "required" => true),
		"song" => array("type" => "int", "required" => true),
		"done_at" => array("type" => "int", "required" => true),
		"type" => array("type" => "text", "required" => true),
		"parameters" => array("type" => "text", "required" => true),
	);
    protected $relations = array(
        "user" => array("hasOne", User::class, "user"),
        "song" => array("hasOne", song::class, "song"),
    );
	public static function getWeeklyUsersLeaderboard($page = 1, $limit = 20): array {
		$startWeek = $today = date::format("j");
		$dayOfTheWeek = date::format("N");
		if ($dayOfTheWeek < 6) {
			$startWeek = $today - ($dayOfTheWeek + 1);
		} else if ($dayOfTheWeek == 7) {
			$startWeek--;
		}
		$start = date::mktime(0, 0, 0, null, $startWeek);
		db::join("ghafiye_contributes", "ghafiye_contributes.user=userpanel_users.id", "INNER");
		$user = new User();
		$user->where("ghafiye_contributes.done_at", $start, ">=");
		$user->groupBy("ghafiye_contributes.user");
		$user->orderBy("userpanel_users.points", "DESC");
		$users = $user->get($limit, "userpanel_users.*");
		return $users;
	}
	public function getHandler(){
		if (!$this->handler) {
			if (!class_exists($this->type)) {
				throw new \TypeError($this->type);
			}
			$this->handler = new $this->type;
			$this->handler->setContribute($this);
		}
		return $this->handler;
	}
}
