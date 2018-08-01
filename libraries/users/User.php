<?php
namespace packages\ghafiye;
use packages\userpanel\user as userpanelUser;

class user extends userpanelUser {
	protected $dbFields = array(
        "email" => array("type" => "text", "required" => true, "unique" => "true"),
        "name" => array("type" => "text", "required" => true),
        "lastname" => array("type" => "text"),
        "password" => array("type" => "text", "required" => true),
        "points" => array("type" => "int"),
    );
}
