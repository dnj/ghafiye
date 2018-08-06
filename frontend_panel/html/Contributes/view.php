<?php
use packages\base\translator;
use packages\userpanel;
use packages\ghafiye\Contribute;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-wrench"></i> <?php echo translator::trans("ghafiye.panel.contributes.view"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<?php if ($this->canEdit and $this->contribute->status === Contribute::waitForAccept) { ?>
				<div class="row">
					<div class="col-sm-3 col-sm-offset-3">
						<a href="<?php echo userpanel\url("contributes/accept/" . $this->contribute->id); ?>" class="btn btn-block btn-sm btn-success"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("ghafiye.accept"); ?></a>
					</div>
					<div class="col-sm-3">
						<a href="<?php echo userpanel\url("contributes/reject/" . $this->contribute->id); ?>" class="btn btn-block btn-sm btn-danger"><i class="fa fa-ban"></i> <?php echo translator::trans("ghafiye.reject"); ?></a>
					</div>
				</div>
				<?php } ?>
				<?php echo $this->contribute->buildFrontend(); ?>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
