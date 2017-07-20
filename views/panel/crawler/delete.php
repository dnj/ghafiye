<?php
namespace packages\ghafiye\views\panel\crawler;
use \packages\ghafiye\crawler\queue;
use \packages\ghafiye\views\form;
class delete extends form{
	public function setQueue(queue $queue){
		$this->setData($queue, "queue");
	}
	protected function getQueue():queue{
		return $this->getData("queue");
	}
}
