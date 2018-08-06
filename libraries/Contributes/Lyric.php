<?php
namespace packages\ghafiye\contribute;
use packages\userpanel\date;
use packages\base\{db, db\dbObject};
use packages\ghafiye\{Contribute, song};

class Lyric  extends dbObject {
	protected $handler;
	protected $dbTable = "ghafiye_contributes_lyrics";
	protected $primaryKey = "id";
	protected $dbFields = array(
		"contribute" => array("type" => "int", "required" => true),
		"parent" => array("type" => "int"),
		"lyric" => array("type" => "int", "required" => true),
		"old_text" => array("type" => "text"),
		"text" => array("type" => "text"),
		"time" => array("type" => "int"),
	);
    protected $relations = array(
        "contribute" => array("hasOne", Contribute::class, "contribute"),
        "lyric" => array("hasOne", song\lyric::class, "lyric"),
    );
}
