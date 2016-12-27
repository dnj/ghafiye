<?php
namespace packages\ghafiye\musixmatch;
use \packages\base\db\dbObject;
class lyric extends dbObject{
	protected $dbTable = "ghafiye_musixmatch_lyrics";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'id' => array('type' => 'int', 'required' => true),
		'trackId' => array('type' => 'int', 'required' => true),
		'canEdit' => array('type' => 'bool'),
		'locked' => array('type' => 'bool'),
		'actionRequested' => array('type' => 'bool'),
		'verified' => array('type' => 'bool'),
		'restricted' => array('type' => 'bool'),
		'instrumental' => array('type' => 'bool'),
		'explicit' => array('type' => 'bool'),
		'body' => array('type' => 'text', 'required' => true),
		'language' => array('type' => 'text', 'required' => true),
		'languageDescription' => array('type' => 'text'),
		'copyright' => array('type' => 'text'),
		'updatedTime' => array('type' => 'text')
	);
	public function save($data = null){
		if(!$this->isNew){
			if($this->id){
				parent::where($this->primaryKey, $this->id);
				$this->isNew = !parent::has();
			}else{
				$this->isNew = true;
			}
		}
		return parent::save($data);
	}
}
