<?php
namespace packages\ghafiye;
use packages\base\{db, json};

trait Paramable{
	protected $tempParams = [];
	public function setParam(string $name, $value) {
		if ($this->isNew or !$this->id) {
			$this->tempParams[$name] = $value;
		} else {
			if ($this->hasParam($name)) {
				$this->updateParam($name, $value);
			} else {
				$this->insertParam($name, $value);
			}
		}
	}
	public function hasParam(string $name) {
		if (isset($this->tempParams[$name])) {
			return true;
		}
		if (!$this->isNew and $this->id) {
			return db::where($this->getObjectName(), $this->id)->where("name", $name)->has($this->getParamsTable());
		}
		return false;
	}
	public function param(string $name) {
		if (isset($this->tempParams[$name])) {
			return $this->tempParams[$name];
		}
		if (!$this->isNew and $this->id) {
			$param = db::where($this->getObjectName(), $this->id)->where("name", $name)->getOne($this->getParamsTable());
			if ($param) {
				return $this->decodeValue($param["value"]);
			}
		}
		return null;
	}
	public function getParams():array{
		$result = [];
		$params = db::where($this->getObjectName(), $this->id)->get($this->getParamsTable(), null, array("name", "value"));
		foreach ($params as $param) {
			$result[$param["name"]] = $this->decodeValue($param["value"]);
		}
		return $result;
	}
	public function save($data = null) {
		$result = parent::save($data);
		if ($result) {
			$this->saveParams();
		}
		return $result;
	}
	protected function saveParams() {
		if (!$this->isNew and $this->id and $this->tempParams) {
			foreach ($this->tempParams as $name => $value) {
				$this->insertParam($name, $value);
			}
		}
	}
	protected function insertParam(string $name, $value) {
		return db::insert($this->getParamsTable(), array(
			$this->getObjectName() => $this->id,
			"name" => $name,
			"value" => $this->encodeValue($value),
		));
	}
	protected function updateParam(string $name, $value) {
		return db::where($this->getObjectName(), $this->id)->where("name", $name)->update($this->getParamsTable(), array(
			"value" => $this->encodeValue($value),
		));
	}
	protected function getParamsTable(): string {
		return $this->dbTable."_params";
	}
	protected function getObjectName(): string {
		$objName = get_class($this);
		$lastBackSlash = strrpos($objName, "\\");
		if ($lastBackSlash !== false) {
			$objName = substr($objName, $lastBackSlash + 1);
		}
		return $objName;
	}
	protected function encodeValue($value): string {
		if (is_array($value)) {
			$value = json\encode($value);
		} else if (is_object($value) and $value instanceof db\dbObject) {
			$value = $this->encodeValue($value->toArray());
		}
		return $value;
	}
	protected function decodeValue(string $value) {
		if (preg_match("/^\\[[a-zA-Z0-9\\,]+\\]$/", $value)) {
			$value = json\decode($value);
		}
		return $value;
	}
}