<?php
namespace packages\ghafiye\crawler\queue;
use packages\base\db\dbObject;
class param extends dbObject{
	protected $dbTable = "ghafiye_crawler_queue_params";
	protected $apiclass;
	protected $primaryKey = "id";
	protected $dbFields = [
        'queue' => ['type' => 'int', 'required' => true],
		'name' => ['type' => 'text', 'required' => true],
		'value' => ['type' => 'text', 'required' => true],
	];
	protected $jsonFields = ['value'];
}
