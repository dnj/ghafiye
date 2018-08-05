<?php
namespace packages\ghafiye\song;
use packages\ghafiye\song;
use packages\base\db\dbObject;

class lyric extends dbObject{
	const published = 1;
	const draft = 2;
	protected $dbTable = "ghafiye_songs_lyrices";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'song' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'parent' => array('type' => 'int'),
		'time' => array('type' => 'int'),
		'text' => array('type' => 'text', 'required' => true),
		"status" => array("type" => "int", "required" => true),
	);
    protected $relations = array(
        "song" => array("hasOne", song::class, "song"),
    );
	protected function is_ltr($lang = null){
		if($lang == null){
			$lang = $this->lang;
		}
		return !in_array($lang, array('ar','fa','dv','he','ps','sd','ur','yi','ug','ku'));
	}
	public function getParent(){
		if(!$this->parent){
			return null;
		}
		return lyric::byId($this->parent);
	}
	public function hasDescription() {
		$description = new lyric\Description();
		$description->where("status", lyric\Description::accepted);
		$description->where("lyric", $this->id);
		return $description->has();
	}
}
