<?php
namespace packages\ghafiye\musixmatch;
use \packages\base\db\dbObject;
class genre extends dbObject{
	protected $dbTable = "ghafiye_musixmatch_genres";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'id' => array('type' => 'int', 'required' => true),
		'parent' => array('type' => 'int'),
		'name' => array('type' => 'text', 'required' => true),
		'name_extended' => array('type' => 'text'),
		'vanity' => array('type' => 'text')
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
