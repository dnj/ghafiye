<?php
namespace themes\clipone\views\settings\usertypes;
use \packages\base\translator;
use \packages\userpanel\usertype;
use \packages\userpanel\views\settings\usertypes\edit as usertypeEdit;
use \themes\clipone\navigation;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
class edit extends usertypeEdit{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('settings'),
			translator::trans('usertype.byTitle', array('title' => $this->getUserType()->title)),
			translator::trans('edit')
		));
		navigation::active("settings/usertypes");
	}
	protected function translatePermission($permission){
		$trans = translator::trans('usertype.permissions.'.$permission);
		return($trans ? $trans : $permission);
	}
	protected function hasPriority(usertype $priority){
		foreach($this->getUserType()->children as $child){
			if($priority->id == $child->data['child']){
				return true;
			}
		}
		return false;
	}
}
