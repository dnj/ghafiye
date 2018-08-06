<?php
use packages\base;
use packages\base\translator;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-white panel-sync">
			<div class="panel-heading">
				<div class="panel-icon"><i class="fa fa-clock-o"></i></div>
				<?php echo translator::trans("ghafiye.contribute.sync.song", array("title" => $this->song->title($this->song->lang))); ?>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo base\url("contribute/song/sync/{$this->song->id}"); ?>" data-song="<?php echo $this->song->id; ?>">
					<div class="row">
						<?php $isLtr = $this->isLtr(); ?>
						<div class="col-xs-12">
							<?php foreach ($this->getLyrics() as $lyric) { ?>
								<div class="panel sync-panel">
									<div class="panel-body">
										<div class="col-sm-3 col-xs-12">
										<?php $this->createField(array(
											"name" => "time[{$lyric->id}]",
											"label" => translator::trans("ghafiye.song.lyric.time"),
											"class" => "form-control sync-input",
											"ltr" => true,
										)); ?>
										</div>
										<div class="col-sm-9 col-xs-12">
											<p class="lyric<?php echo $isLtr ? " ltr" : ""; ?>">
												<?php echo $lyric->text; ?>
											</p>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4 col-xs-12">
							<button type="submit" class="btn btn-block btn-default"><i class="fa fa-clock-o"></i> <?php echo translator::trans("ghafiye.contribute.sync"); ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
