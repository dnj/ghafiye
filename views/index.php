<?php
namespace packages\ghafiye\views;
use \packages\ghafiye\views\form;
class index extends form{
	protected $plans;
	public function setPlans($plans){
		$this->plans = $plans;
	}
	public function getPlans(){
		return $this->plans;
	}
}
