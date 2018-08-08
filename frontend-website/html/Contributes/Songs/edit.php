<?php
use packages\base;
use packages\base\translator;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-white panel-edit">
			<div class="panel-heading">
				<div class="panel-icon"><i class="fa fa-pencil"></i></div>
				<?php echo translator::trans("ghafiye.contribute.edit.song", array("title" => $this->song->title($this->song->lang))); ?>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo base\url("contribute/song/edit/{$this->song->id}"); ?>">
					<div class="row">
						<?php $isLtr = $this->isLtr(); ?>
						<div class="col-sm-4 col-xs-12">
							<div class="row">
								<div class="col-sm-8 col-sm-offset-2">
									<label class="control-label"><?php echo translator::trans("ghafiye.song.image"); ?></label>
									<div class="fileupload fileupload-new" data-provides="fileupload">
										<div class="form-group">
											<div class="user-image avatarPreview">
												<img src="<?php echo $this->getImage(); ?>" class="preview img-responsive">
												<input name="image" type="file">
												<div class="button-group">
													<button type="button" class="btn btn-teal btn-sm btn-upload"><i class="fa fa-pencil"></i></button>
													<button type="button" class="btn btn-bricky btn-sm btn-remove" data-default="<?php echo $this->getImage(); ?>"><i class="fa fa-times"></i></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
								<?php $this->createField(array(
									"name" => "title",
									"label" => translator::trans("ghafiye.song.title"),
									"ltr" => $isLtr,
								)); ?>
								</div>
							</div>
						</div>
						<div class="col-sm-8 col-xs-12">
							<?php
							$i = 0;
							foreach ($this->getLyrics() as $lyric) {
							?>
								<div class="panel edit-panel">
									<div class="panel-body">
										<div class="input-group">
											<?php
											$this->createField(array(
												"name" => "lyrics[{$i}][id]",
												"class" => "lyric-id",
												"type" => "hidden",
											));
											$this->createField(array(
												"name" => "lyrics[{$i}][text]",
												"class" => "form-control edit-input",
												"ltr" => $isLtr,
											));
											$i++;
											?>
											<div class="input-group-btn">
												<button type="button" class="btn btn-default btn-add tooltips" title="افزودن">
													<i class="fa fa-plus"></i>
												</button>
												<button type="button" class="btn btn-default btn-up tooltips" title="انتقال به بالا">
													<i class="fa fa-arrow-up"></i>
												</button>
												<button type="button" class="btn btn-default btn-down tooltips" title="انتقال به پایین">
													<i class="fa fa-arrow-down"></i>
												</button>
												<button type="button" class="btn btn-default btn-delete tooltips" title="حذف">
													<i class="fa fa-trash"></i>
												</button>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4 col-xs-12">
							<button type="submit" class="btn btn-sm btn-block btn-edit">
								<div class="btn-icon"><i class="fa fa-pencil"></i></div>
								<?php echo translator::trans("ghafiye.contribute.edit"); ?>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
