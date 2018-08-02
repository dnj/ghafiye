<?php
use packages\base;
use packages\base\translator;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-white panel-add-song">
			<div class="panel-heading">
				<div class="panel-icon"><i class="fa fa-music"></i></div>
				<?php echo translator::trans("ghafiye.contribute.add.song"); ?>
			</div>
			<div class="panel-body">
				<form method="post">
					<div class="row">
						<div class="col-sm-5 col-sm-push-7 col-xs-12">
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
									<?php
									$this->createField(array(
										"type" => "hidden",
										"name" => "person",
									));
									$this->createField(array(
										"name" => "person_name",
										"label" => translator::trans("ghafiye.singer"),
									));
									?>
									<p><small>نام شخص را در فیلد بالا وارد و آن را در بین نتایج انتخاب کنید</small></p>
									<p><small>در صورتی که شخص مد نظر شما در بین نتایج نیست، میتوانید از <a href="#">اینجا</a> آن را اضافه کنید</small></p>
									<p><small>اگر بیشتر از یک خواننده در این آهنگ شرکت کرده اند، از فیلد گروه استفاده کنید</small></p>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<?php
									$this->createField(array(
										"type" => "hidden",
										"name" => "group",
									));
									$this->createField(array(
										"name" => "group_name",
										"label" => translator::trans("ghafiye.group"),
									));
									?>
									<p><small>نام گروه را در فیلد بالا وارد و آن را در بین نتایج انتخاب کنید</small></p>
									<p><small>در صورتی که گروه مد نظر شما در بین نتایج نیست، میتوانید از <a href="#">اینجا</a> آن را اضافه کنید</small></p>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<?php
									$this->createField(array(
										"type" => "hidden",
										"name" => "album",
									));
									$this->createField(array(
										"name" => "album_name",
										"label" => translator::trans("ghafiye.album"),
									));
									?>
									<p><small>نام آلبوم را در فیلد بالا وارد و آن را در بین نتایج انتخاب کنید</small></p>
									<p><small>در صورتی که آلبوم مد نظر شما در بین نتایج نیست، میتوانید از <a href="#">اینجا</a> آن را اضافه کنید</small></p>
								</div>
							</div>
						</div>
						<div class="col-sm-7 col-sm-pull-5 col-xs-12">
							<div class="row">
								<div class="col-sm-7 col-xs-12">
									<?php $this->createField(array(
										"name" => "title",
										"label" => translator::trans("ghafiye.song.title"),
									)); ?>
									<div class="row">
										<div class="col-xs-12">
											<?php $this->createField(array(
												"name" => "genre",
												"type" => "select",
												"label" => translator::trans("ghafiye.song.genre"),
												"options" => $this->getGenresForSelect(),
											)); ?>
										</div>
									</div>
								</div>
								<div class="col-sm-5 col-xs-12">
									<?php $this->createField(array(
										"type" => "select",
										"name" => "lang",
										"label" => translator::trans("ghafiye.song.lang"),
										"options" => $this->getLangsForSelect(),
									)); ?>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<?php $this->createField(array(
										"type" => "textarea",
										"name" => "lyrics",
										"label" => translator::trans("ghafiye.song.lyrics"),
										"rows" => 20,
									)); ?>
									<p><small>هر خط از متن آهنگ را در یک خط ار فیلد بالا وارد کنید</small></p>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4 col-xs-12">
							<button type="submit" class="btn btn-block btn-success"><i class="fa fa-check-square-o"></i> افزودن</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
