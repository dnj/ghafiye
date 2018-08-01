<?php
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\date;
use \packages\ghafiye\song\lyric\Description;
use \themes\clipone\utility;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-edit"></i> <?php echo translator::trans("ghafiye.panel.songs.lyrics.descriptions.edit"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<form action="<?php echo userpanel\url("songs/lyrics/descriptions/edit/{$this->description->id}"); ?>" method="POST">
					<div class="row">
						<div class="col-sm-6 col-xs-12">
							<div class="form-group">
								<label class="col-xs-12"><?php echo translator::trans("ghafiye.panel.song.lyrics.descriptions.lyric"); ?>:</label>
								<div class="col-xs-12"><?php echo $this->description->lyric->text; ?></div>
							</div>
							<div class="form-group">
								<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.song.lyrics.descriptions.user"); ?>:</label>
								<div class="col-xs-7"><?php echo $this->description->user->getFullName(); ?></div>
							</div>
							<div class="form-group">
								<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.song.lyrics.descriptions.sent_at"); ?>:</label>
								<div class="col-xs-7 ltr"><?php echo date::format("Y/m/d H:i", $this->description->sent_at); ?></div>
							</div>
							<div class="form-group">
								<label class="col-xs-12"><?php echo translator::trans("ghafiye.panel.song.lyrics.descriptions.status"); ?>:</label>
								<div class="col-xs-12">
									<?php $this->createField(array(
										"name" => "status",
										"type" => "select",
										"options" => $this->getStatusForSelect(),
									)); ?>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-xs-12">
							<?php $this->createField(array(
								"label" => translator::trans("ghafiye.panel.song.lyrics.descriptions.text"),
								"name" => "text",
								"type" => "textarea",
								"rows" => 7,
							)); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4 pull-left text-left col-xs-12">
							<button type="submit" class="btn btn-teal"><?php echo translator::trans("edit"); ?></button>
							<a href="<?php echo userpanel\url("songs/lyrics/descriptions"); ?>" class="btn btn-default"><?php echo translator::trans("ghafiye.return"); ?></a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
