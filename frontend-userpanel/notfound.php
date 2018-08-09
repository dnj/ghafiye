<?php
use packages\userpanel;
use packages\base\translator;
use packages\userpanel\authorization;
$header = "";
if ($this->loged_in) {
	$header = "login";
}
if (!authorization::is_accessed("can_login_in_userpanel")) {
	$header = "ghafiye";
}
$this->the_header($header);
?>
<span class="errorcode">404</span>
<h1><?php echo translator::trans('notfound.title'); ?></h1>
<p class="errordescription"><?php echo translator::trans('notfound.description'); ?></p>
<?php
$this->the_footer();

