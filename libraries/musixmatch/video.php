<?php
namespace packages\ghafiye\musixmatch;
use \packages\base\db\dbObject;
class video extends dbObject{
	protected $dbTable = "ghafiye_musixmatch_videos";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'id' => array('type' => 'int', 'required' => true),
		'title' => array('type' => 'text', 'required' => true),
		'artist' => array('type' => 'int'),
		'identifier' => array('type' => 'text', 'required' => true, 'unique' => true),
		'source' => array('type' => 'bool'),
		'length' => array('type' => 'int'),
		'url' => array('type' => 'text'),
		'thumbnail' => array('type' => 'text'),
		'hasLyrics' => array('type' => 'bool'),
		'hasSubtitles' => array('type' => 'bool'),
		'mobileEmbeddable' => array('type' => 'bool'),
		'countryDenied' => array('type' => 'text'),
		'countryAllowed' => array('type' => 'text'),
		'embeddable' => array('type' => 'bool'),
		'trackId' => array('type' => 'int'),
		'shareUrl' => array('type' => 'text'),
		'instrumental' => array('type' => 'bool')
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
