<?php
namespace packages\ghafiye;
use packages\base\db\dbObject;
use packages\ghafiye\translator\title;
class group extends dbObject{
	use title, imageTrait;
	const accepted = 1;
	const waitForAccept = 2;
	const rejected = 3;
	protected $dbTable = "ghafiye_groups";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'avatar' => array('type' => 'text'),
        'lang' => array('type' => 'text', 'required' => true),
		'cover' => ['type' => 'text'],
		"status" => array("type" => "int", "required" => true),
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
	public function getAvatar(int $width, int $height){
		return $this->getImage($width, $height, 'avatar');
	}
	public function getCover(int $width, int $height){
		return $this->getImage($width, $height, 'cover');
	}
	public function encodedName($lang = null){
		return $this->encodedTitle($lang);
	}
	public function name($lang = null){
		return $this->title($lang);
	}
}
