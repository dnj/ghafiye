<?php
use packages\base\translator;
use packages\userpanel;
use packages\userpanel\date;
use packages\ghafiye\Contribute;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-success">
			<div class="panel-heading">
				<i class="fa fa-check-square-o"></i> <?php echo translator::trans("ghafiye.panel.contributes.accept"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-xs-12 form-horizontal">
								<div class="form-group">
									<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.contributes.id"); ?>:</label>
									<div class="col-xs-7 ltr">#<?php echo $this->contribute->id; ?></div>
								</div>
								<div class="form-group">
									<label class="col-sm-5"><?php echo translator::trans("ghafiye.panel.contributes.title"); ?>:</label>
									<div class="col-sm-7"><?php echo $this->contribute->title; ?></div>
								</div>
								<div class="form-group">
									<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.contributes.user"); ?>:</label>
									<div class="col-xs-7">
										<a href="<?php echo userpanel\url("users", array("id" => $this->contribute->user->id)); ?>">
											<?php echo $this->contribute->user->getFullName(); ?>
										</a>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.contributes.doneAt"); ?>:</label>
									<div class="col-xs-7 ltr"><?php echo date::format("Y/m/d H:i", $this->contribute->done_at); ?></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<form action="<?php echo userpanel\url("contributes/accept/{$this->contribute->id}"); ?>" method="POST">
							<div class="row">
								<div class="col-xs-12">
									<div class="alert alert-success" role="alert">
										آیا از تایید مشارکت اطمینان دارید ؟
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6 col-xs-12">
									<button type="submit" class="btn btn-success"><?php echo translator::trans("ghafiye.accept"); ?></button>
									<a href="<?php echo userpanel\url("contributes"); ?>" class="btn btn-default"><?php echo translator::trans("ghafiye.return"); ?></a>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
