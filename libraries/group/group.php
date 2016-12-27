<?php
namespace packages\ghafiye;
use packages\base\db\dbObject;
use packages\ghafiye\translator\title;
class group extends dbObject{
	use title;
	protected $dbTable = "ghafiye_groups";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'avatar' => array('type' => 'text'),
        'lang' => array('type' => 'text', 'required' => true)
	);
    protected $relations = array(
		'titles' => array("hasMany", "packages\\ghafiye\\group\\title", "group_id"),
		'persons' => array("hasMany", "packages\\ghafiye\\group\\person", "group_id")
    );
	public function getTitle(){
		foreach($this->titles as $title){
			if($title->lang == $this->lang){
				return $title->title;
			}
		}
		return false;
	}
}
