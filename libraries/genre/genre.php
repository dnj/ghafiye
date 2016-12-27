<?php
namespace packages\ghafiye;
use packages\base\db;
use packages\base\db\dbObject;
use packages\ghafiye\song;
use packages\ghafiye\translator\title;
class genre extends dbObject{
	use title;
	protected $dbTable = "ghafiye_genres";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'musixmatch_id' => array('type' => 'int', 'unique' => true)
	);
    protected $relations = array(
        'titles' => array("hasMany", "packages\\ghafiye\\genre\\title", "genre")
    );
	public static function getActives($limit = null){
		$genre = new genre();
		$genre->join("packages\\ghafiye\\song", null, "inner", "genre");
		$genre->having("COUNT(ghafiye_songs.id)", 0, '>');
		$genre->where("ghafiye_songs.status", song::publish);
		$genre->groupBy('ghafiye_songs.id');
		$genre->setQueryOption('DISTINCT');
		return $genre->get($limit, "ghafiye_genres.*");
	}
}
