<?php
namespace packages\ghafiye;
use packages\userpanel\date;
use packages\base\{db, db\dbObject};

class Contribute extends dbObject {
	const accepted = 1;
	const waitForAccept = 2;
	const rejected = 3;
	protected $handler;
	protected $dbTable = "ghafiye_contributes";
	protected $primaryKey = "id";
	protected $dbFields = array(
		"title" => array("type" => "text", "required" => true),
		"user" => array("type" => "int", "required" => true),
		"song" => array("type" => "int"),
		"person" => array("type" => "int"),
		"album" => array("type" => "int"),
		"groupID" => array("type" => "int"),
		"lang" => array("type" => "text"),
		"done_at" => array("type" => "int", "required" => true),
		"type" => array("type" => "text", "required" => true),
		"parameters" => array("type" => "text"),
		"point" => array("type" => "int", "required" => true),
		"status" => array("type" => "int", "required" => true),
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
		$contribute = new static();
		$contribute->where("ghafiye_contributes.done_at", $start, ">=");
		$contribute->groupBy("ghafiye_contributes.user");
		$contribute->orderBy("`cpoints`", "DESC");
		$contributes = $contribute->get($limit, array("ghafiye_contributes.user", "SUM(ghafiye_contributes.point) as `cpoints`"));
		return $contributes;
	}
	public function getHandler() {
		if (!$this->handler) {
			if (!class_exists($this->type)) {
				throw new \TypeError($this->type);
			}
			$this->handler = new $this->type;
			$this->handler->setContribute($this);
		}
		return $this->handler;
	}
	public function getPoint(): int {
		return $this->getHandler()->getPoint();
	}
	public function getImage(int $width, int $height) {
		return $this->getHandler()->getImage($width, $height);
	}
	public function getPreviewContent(): string {
		return $this->getHandler()->getPreviewContent();
	}
	public function buildFrontend(): string {
		return $this->getHandler()->buildFrontend();
	}
	protected function preLoad(array $data): array {
		if (!isset($data["status"])) {
			$data["status"] = self::waitForAccept;
		}
		if (!isset($data["done_at"])) {
			$data["done_at"] = date::time();
		}
		return $data;
	}
}
