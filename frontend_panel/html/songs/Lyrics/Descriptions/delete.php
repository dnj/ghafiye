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
		<div class="panel panel-danger">
			<div class="panel-heading">
				<i class="fa fa-trash"></i> <?php echo translator::trans("ghafiye.panel.songs.lyrics.descriptions.delete"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<form action="<?php echo userpanel\url("songs/lyrics/descriptions/delete/{$this->description->id}"); ?>" method="POST">
					<div class="row">
						<div class="col-xs-12">
							<div class="alert alert-danger">
								<p><?php echo translator::trans("ghafiye.panel.songs.lyrics.descriptions.delete.notice"); ?></p>
							</div>
						</div>
					</div>
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
								<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.song.lyrics.descriptions.status"); ?>:</label>
								<div class="col-xs-7 text-left">
									<?php 
									$statusClass = utility::switchcase($this->description->status, array(
										"label label-success" => Description::accepted,
										"label label-warning" => Description::waitForAccept,
										"label label-inverse" => Description::rejected,
									));
									$statusTxt = utility::switchcase($this->description->status, array(
										"ghafiye.panel.song.lyrics.descriptions.status.accepted" => Description::accepted,
										"ghafiye.panel.song.lyrics.descriptions.status.waitForAccept" => Description::waitForAccept,
										"ghafiye.panel.song.lyrics.descriptions.status.rejected" => Description::rejected,
									));
									?>
									<span class="<?php echo $statusClass; ?>"><?php echo translator::trans($statusTxt); ?></span>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-xs-12">
							<div class="form-group">
								<label class="col-xs-12"><?php echo translator::trans("ghafiye.panel.song.lyrics.descriptions.text"); ?>:</label>
								<div class="col-xs-12"><?php echo nl2br($this->description->text); ?></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4 pull-left text-left col-xs-12">
							<button type="submit" class="btn btn-danger"><?php echo translator::trans("ghafiye.delete"); ?></button>
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
