<?php
namespace packages\ghafiye;
use packages\base\{http, json, router, router\rule, Packages};

foreach (router::getRules() as $rule) {
	$path = $rule->getPath();
	if (isset($path[0]["name"]) and $path[0]["name"] == "userpanel") {
		$name = "";
		if (isset($path[1])) {
			$name = $path[1]["name"];
		}
		if ($name) {
			if (in_array($name, array("login", "register", "resetpwd", "logout"))) {
				continue;
			}
		}
		$rule->addMiddleware("\\packages\\ghafiye\\controllers\\Login", "validate");
	}
}
