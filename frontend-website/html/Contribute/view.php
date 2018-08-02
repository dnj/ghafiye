<?php
use packages\base;
use packages\base\translator;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-white panel-view-contribute">
			<div class="panel-heading">
				<div class="panel-icon"><i class="fa fa-trophy"></i></div>
				<a href="<?php echo base\url("profile/{$this->contribute->user->id}"); ?>"> 
				<?php echo $this->contribute->user->getFullName(); ?></a><?php echo $this->contribute->title; ?>
				<span class="badge"><?php echo translator::trans("ghafiye.contribute.point", array("point" => $this->contribute->getPoint())); ?></span>
			</div>
			<div class="panel-body">
				<?php echo $this->contribute->buildFrontend(); ?>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
