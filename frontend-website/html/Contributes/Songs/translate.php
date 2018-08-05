<?php
use packages\base;
use packages\base\translator;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-white panel-translate">
			<div class="panel-heading">
				<div class="panel-icon"><i class="fa fa-music"></i></div>
				<?php echo translator::trans("ghafiye.contribute.translate.song", array("title" => $this->song->title($this->song->lang))); ?>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo base\url("contribute/song/translate/{$this->song->id}"); ?>" data-song="<?php echo $this->song->id; ?>">
					<div class="row">
						<div class="col-sm-4 col-xs-12">
							<div class="row">
								<div class="col-xs-12">
								<?php
								$this->createField(array(
									"name" => "lang",
									"type" => "select",
									"label" => translator::trans("ghafiye.lang"),
									"options" => $this->getLangsForSelect(),
								));
								$this->createField(array(
									"name" => "title",
									"label" => translator::trans("ghafiye.song.title"),
								));
								?>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 translate-progress">
									<p>درصد ترجمه شده: </p>
									<div class="progress">
										<div class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
										<span class="percent">0%</span>
									</div>
								</div>
							</div>
						</div>
						<?php $isLtr = $this->isLtr(); ?>
						<div class="col-sm-8 col-xs-12">
							<?php foreach ($this->getLyrics() as $lyric) { ?>
								<div class="panel translate-panel">
									<div class="panel-body">
										<p class="lyric<?php echo $isLtr ? " ltr" : ""; ?>">
											<?php echo $lyric->text; ?>
										</p>
										<?php $this->createField(array(
											"name" => "translates[{$lyric->id}]",
											"class" => "form-control tanslate-input",
										)); ?>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4 col-xs-12">
							<button type="submit" class="btn btn-block btn-default"><i class="fa fa-check-square-o"></i> ترجمه</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
