<?php
namespace packages\ghafiye\crawler;
use packages\base\db\dbObject;
use packages\ghafiye\crawler\queue\param;
class queue extends dbObject{
	const artist = 1;
	const genre = 2;
	const track = 3;
	const album = 4;
	const types = [
		'artist' => self::artist,
		'genre' => self::genre,
		'track' => self::track,
		'album' => self::album
	];
	const passed = 1;
	const queued = 2;
	const running = 3;
	const faild = 4;
	const statuses = [
		'passed' => self::passed,
		'queued' => self::queued,
		'running' => self::running,
		'faild' => self::faild
	];
	protected $dbTable = "ghafiye_crawler_queue";
	protected $primaryKey = "id";
	protected $dbFields = [
        'type' => ['type' => 'int', 'required' => true],
        'MMID' => ['type' => 'int'],
        'status' => ['type' => 'int', 'required' => true],
	];
	protected $relations = [
		'params' => ['hasMany', 'packages\\ghafiye\\queue\\param', 'queue']
	];
	protected $tmparams = [];
	public function setParam(string $name, $value){
		$param = false;
		foreach($this->params as $p){
			if($p->name == $name){
				$param = $p;
				break;
			}
		}
		if(!$param){
			$param = new param([
				'name' => $name,
				'value' => $value
			]);
		}else{
			$param->value = $value;
		}
		if($this->isNew or !$this->id){
			$this->tmparams[$name] = $param;
		}else{
			$param->queue = $this->id;
			return $param->save();
		}
	}
	public function param(string $name){
		if(!$this->id){
			return(isset($this->tmparams[$name]) ? $this->tmparams[$name]->value : null);
		}else{
			foreach($this->params as $param){
				if($param->name == $name){
					return $param->value;
				}
			}
			return false;
		}
	}
	public function deleteParam(string $name):bool{
		if(!$this->id){
			if(isset($this->tmparams[$name])){
				unset($this->tmparams[$name]);
				return true;
			}
		}else{
			$param = new param();
			$param->where("queue", $this->id);
			$param->where('name', $name);
			if($param->getOne()){
				return $param->delete();
			}
		}
		return false;
	}
	public function save($data = null){
		if(($return = parent::save($data))){
			foreach($this->tmparams as $param){
				$param->queue = $this->id;
				$param->save();
			}
			$this->tmparams = [];
		}
		return $return;
	}
}
