<?php
namespace packages\ghafiye\song;
use packages\base\db\dbObject;
class lyric extends dbObject{
	protected $dbTable = "ghafiye_songs_lyrices";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'song' => array('type' => 'int', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
		'parent' => array('type' => 'int'),
		'time' => array('type' => 'int'),
		'text' => array('type' => 'text', 'required' => true),
	);
	protected function is_ltr($lang = null){
		if($lang == null){
			$lang = $this->lang;
		}
		return !in_array($lang, array('ar','fa','dv','he','ps','sd','ur','yi','ug','ku'));
	}
}
