<?php
use packages\base;
use packages\base\translator;
$this->the_header();
?>
<div class="row">
	<div class="col-sm-8 col-sm-push-4">
		<div class="panel panel-white panel-translate">
			<div class="panel-heading">
				<div class="panel-icon"><i class="fa fa-globe"></i></div>
				<?php echo translator::trans("ghafiye.contribute.translate"); ?>
				<span class="badge"><?php echo translator::trans("ghafiye.contribute.point", array("point" => $this->translatePoint)); ?></span>
			</div>
			<div class="panel-body">
				<ul class="list-group tracks">
					<?php foreach ($this->getTranslateTracks() as $track) { ?>
					<li class="list-group-item">
						<div class="row">
							<div class="col-sm-1 col-xs-2 track-img">
								<a href="<?php echo $track->url(); ?>">
									<img src="<?php echo $track->getImage(32, 32); ?>" alt="<?php echo $track->title(); ?>">
								</a>
							</div>
							<div class="col-sm-8 col-xs-7">
								<a class="track-title" href="<?php echo $track->url(); ?>"><?php echo $track->title(); ?></a>
								<a class="track-singer-name" href="<?php echo base\url($track->getSinger()->encodedName()); ?>"><?php echo $track->getSinger()->name(); ?></a>
							</div>
							<div class="col-sm-3 col-xs-3">
								<a href="<?php echo base\url("contribute/song/translate/{$track->id}"); ?>" class="btn btn-sm btn-block btn-default btn-translate"><span class="hidden-xs"><?php echo translator::trans("ghafiye.contribute.translate"); ?></span><span class="visible-xs"><i class="fa fa-edit"></i></span></a>
							</div>
						</div>
					</li>
					<?php } ?>
				</ul>
			</div>
			<div class="panel-footer">
				<a class="btn-more-translate-track" href="<?php echo base\url("contribute/translate"); ?>"><?php echo translator::trans("ghafiye.contribute.load.more"); ?></a>
			</div>
		</div>
	</div>
	<div class="col-sm-4 col-sm-pull-8">
		<div class="panel panel-default profile-info">
			<div class="panel-body">
				<div class="row user-info">
					<div class="col-sm-4 col-xs-5">
						<img src="<?php echo $this->user->getAvatar(82, 82); ?>" alt="<?php echo $this->user->getFullName(); ?>" class="img-responsive img-circle">
					</div>
					<div class="col-sm-8 col-xs-7">
						<p class="h2"><?php echo $this->user->getFullName(); ?></p>
						<span><?php echo translator::trans("ghafiye.contribute.point", array("point" => $this->user->points)); ?></span>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<ul class="list-group">
							<li class="list-group-item">
								<a href="<?php echo base\url("song/add"); ?>">
									<?php echo translator::trans("ghafiye.contribute.add.song"); ?>
								</a>
							</li>
							<li class="list-group-item">
								<a href="<?php echo base\url("singer/add"); ?>">
									<?php echo translator::trans("ghafiye.contribute.add.singer"); ?>
								</a>
							</li>
							<li class="list-group-item">
								<a href="<?php echo base\url("group/add"); ?>">
									<?php echo translator::trans("ghafiye.contribute.add.group"); ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="alert alert-warning">
			<?php echo translator::trans("ghafiye.contribute.allert"); ?>
		</div>
		<div class="panel panel-white">
			<div class="panel-heading">
				<div class="panel-icon">
					<i class="fa fa-trophy"></i>
				</div>
				<?php echo translator::trans("ghafiye.contribute.weekly.leag.panel.title"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body contributors">
				<ul class="list-group">
					<?php
					$length = count($this->users);
					for ($i = 0; $i < $length; $i++) {
						$user = $this->users[$i];
						$level = $i + 1;
					?>
					<li class="list-group-item<?php $this->user->id == $user->id ? " active" : ""; ?>">
						<a href="<?php echo base\url("profile/{$user->id}"); ?>">
							<span class="badge<?php echo $i < 3 ? " badge-{$level}" : ""; ?>"><?php echo $level; ?></span>
							<div class="user-avatar">
								<img src="<?php echo $user->getAvatar(32, 32); ?>" class="img-responsive img-circle" ‌title="مشاهده پروفایل">
							</div>
							<div class="user-name">
								<p><?php echo $user->getFullName(); ?></p>
								<small><?php echo translator::trans("ghafiye.contribute.point", array("point" => $user->points)) ?></small>
							</div>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
