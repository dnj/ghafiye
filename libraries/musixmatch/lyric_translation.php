<?php
namespace packages\ghafiye\musixmatch\lyric;
use \packages\base\db\dbObject;
class translation extends dbObject{
	protected $dbTable = "ghafiye_musixmatch_lyrics_translations";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'id' => array('type' => 'text', 'required' => true),
		'lyricsId' => array('type' => 'int'),
		'commontrackId' => array('type' => 'int'),
		'selectedLanguage' => array('type' => 'text', 'required' => true),
		'languageFrom' => array('type' => 'text'),
		'description' => array('type' => 'text', 'required' => true),
		'snippet' => array('type' => 'text', 'required' => true)
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
