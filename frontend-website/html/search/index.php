<?php
use \packages\base\translator;
$this->the_header();
?>
<span class="errorcode">404</span>
<h1><?php echo translator::trans('notfound.title'); ?></h1>
<p class="errordescription"><?php echo translator::trans('notfound.description'); ?></p>
<?php $this->the_footer(); ?>
