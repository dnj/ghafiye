<?php
namespace packages\ghafiye\musixmatch\track;
use \packages\base\db\dbObject;
class cover extends dbObject{

	protected $dbTable = "ghafiye_musixmatch_tracks_covers";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'track' => array('type' => 'int', 'required' => true),
		'size' => array('type' => 'text', 'required' => true),
		'image' => array('type' => 'text', 'required' => true),
	);
}
