<?php
namespace packages\ghafiye\listeners\settings;
use \packages\userpanel\usertype\permissions;
class usertype{
	public function permissions_list(){
		$permissions = array(
			'persons_list',
			'person_add',
			'person_edit',
			'person_delete',
			'person_name_add',
			'person_name_delete',
		);
		foreach($permissions as $permission){
			permissions::add('ghafiye_'.$permission);
		}
	}
}
