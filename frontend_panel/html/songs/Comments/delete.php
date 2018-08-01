<?php
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\date;
use \packages\ghafiye\song\Comment;
use \themes\clipone\utility;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-danger">
			<div class="panel-heading">
				<i class="fa fa-trash"></i> <?php echo translator::trans("ghafiye.panel.songs.comments.delete"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-5">
						<div class="row">
							<div class="col-xs-12 form-horizontal">
								<div class="form-group">
									<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.songs.comments.id"); ?>:</label>
									<div class="col-xs-7 ltr">#<?php echo $this->comment->id; ?></div>
								</div>
								<div class="form-group">
									<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.songs.comments.name"); ?>:</label>
									<div class="col-xs-7"><?php echo $this->comment->name; ?></div>
								</div>
								<div class="form-group">
									<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.songs.comments.email"); ?>:</label>
									<div class="col-xs-7 ltr"><?php echo $this->comment->email; ?></div>
								</div>
								<div class="form-group">
									<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.songs.comments.song"); ?>:</label>
									<div class="col-xs-7"><?php echo $this->comment->song->title(); ?></div>
								</div>
								<div class="form-group">
									<?php
									$statusClass = utility::switchcase($this->comment->status, array(
										"label label-success" => Comment::accepted,
										"label label-warning" => Comment::waitForAccept,
										"label label-inverse" => Comment::rejected,
									));
									$statusTxt = utility::switchcase($this->comment->status, array(
										"ghafiye.panel.song.comments.status.accept" => Comment::accepted,
										"ghafiye.panel.song.comments.status.waitForAccept" => Comment::waitForAccept,
										"ghafiye.panel.song.comments.status.rejected" => Comment::rejected,
									));
									?>
									<label class="col-xs-5"><?php echo translator::trans("ghafiye.panel.song.comments.status"); ?>:</label>
									<div class="col-xs-7"><span class="<?php echo $statusClass; ?>"><?php echo translator::trans($statusTxt); ?></span></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-7">
						<form action="<?php echo userpanel\url("songs/comments/delete/{$this->comment->id}"); ?>" method="POST">
							<div class="row">
								<div class="col-xs-12">
									<div class="alert alert-danger" role="alert">
										در حذف نظرات دقت کنید . این عملیات بدون بازگشت است .
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6 col-xs-12">
									<button type="submit" class="btn btn-danger"><?php echo translator::trans("ghafiye.delete"); ?></button>
									<a href="<?php echo userpanel\url("songs/comments"); ?>" class="btn btn-default"><?php echo translator::trans("ghafiye.return"); ?></a>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="accept-comment" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans("ghafiye.accept"); ?></h4>
	</div>
	<div class="modal-body">
		<form id="commentsAccept" class="form-horizontal" action="<?php echo userpanel\url("songs/comments/edit/{$this->comment->id}"); ?>" method="POST">
			<input type="hidden" name="status" value="<?php echo Comment::accepted; ?>">
			<p>آیا از پذیرفتن نظر اطمینان دارید ؟</p>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="commentsAccept" class="btn btn-success"><?php echo translator::trans("ghafiye.accept"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans("cancel"); ?></button>
	</div>
</div>
<div class="modal fade" id="reject-comment" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans("ghafiye.reject"); ?></h4>
	</div>
	<div class="modal-body">
		<form id="commentsReject" class="form-horizontal" action="<?php echo userpanel\url("songs/comments/edit/{$this->comment->id}"); ?>" method="POST">
			<input type="hidden" name="status" value="<?php echo Comment::rejected; ?>">
			<p>آیا از رد نظر اطمینان دارید ؟</p>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="commentsReject" class="btn btn-danger"><?php echo translator::trans("ghafiye.reject"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans("cancel"); ?></button>
	</div>
</div>
<?php
$this->the_footer();
