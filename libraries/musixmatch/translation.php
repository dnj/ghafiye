<?php
namespace packages\ghafiye\musixmatch\track;
use \packages\base\db\dbObject;
class translation extends dbObject{
	protected $dbTable = "ghafiye_musixmatch_tracks_translations";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'track' => array('type' => 'text', 'required' => true),
		'fromlang' => array('type' => 'text', 'required' => true),
		'tolang' => array('type' => 'text', 'required' => true),
		'perc' => array('type' => 'int')
	);
}
