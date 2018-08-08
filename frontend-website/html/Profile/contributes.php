<?php
use packages\base;
use packages\base\date;
use packages\ghafiye\song\person;
$this->the_header();
?>
<div class="profile">
	<div class="profile-container">
		<div class="row">
			<div class="col-sm-5">
				<div class="panel panel-default profile-info">
					<div class="panel-body">
						<div class="row">
							<div class="col-xs-12">
								<img src="<?php echo $this->user->getAvatar(82, 82); ?>" alt="<?php echo $this->user->getFullName(); ?>" class="img-responsive img-circle">
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<p class="h2"><?php echo $this->user->getFullName(); ?></p>
								<span><?php echo $this->user->points; ?> امتیاز</span>
							</div>
						</div>
					</div>
				</div>
				<?php
				$songs = $this->getFavoritSongs();
				if ($songs) {
				?>
				<div class="panel panel-default favorit-songs">
					<div class="panel-heading">
						علاقه مندی ها
					</div>
					<div class="panel-body">
						<ul class="list-group">
							<?php
								foreach ($this->getFavoritSongs() as $song) {
									$singer = $song->getPerson(person::singer);
							?>
							<li class="list-group-item">
								<div class="row">
									<div class="col-sm-2 col-xs-3">
										<a target="_blank" href="#" class="user-avatar">
											<img src="<?php echo $song->getImage(32, 32); ?>" class="img-responsive img-circle">
										</a>
									</div>
									<div class="sol-sm-10 col-xs-9">
										<p><a target="_blank" href="<?php echo base\url($singer->encodedName().'/'.$song->encodedTitle()); ?>"><?php echo $song->title(); ?></a></p>
										<p><a class="text-muted" target="_blank" href="<?php echo base\url($singer->encodedName()); ?>"><?php echo $singer->name(); ?></a></p>
									</div>
								</div>
							</li>
							<?php } ?>
						</ul>
					</div>
					<div class="panel-footer text-center">
						<a href="<?php echo base\url("profile/favorites/{$this->user->id}"); ?>" class="btn btn-bloc">بیشتر</a>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="col-sm-7">
				<div class="contributes">
					<p class="title">فعالیت ها</p>
				<?php
				$contributes = $this->getContributes();
				if ($contributes) {
					$length = count($contributes);
					foreach ($contributes as $contribute) {
				?>
					<div class="row">
						<div class="col-xs-12">
							<div class="contribute-info">
								<time class="tooltips" title="<?php echo date::format("Y/m/d H:i", $contribute->done_at); ?>"><?php echo date::relativeTime($contribute->done_at); ?></time>
								<div class="row">
									<div class="col-sm-1 col-xs-2">
										<div class="contributor-avatar">
											<img src="<?php echo $contribute->user->getAvatar(32, 32); ?>" class="img-responsive img-circle" ‌title="مشاهده پروفایل">
										</div>
									</div>
									<div class="col-sm-10 col-xs-7">
										<div class="contributor-name">
											<a href="#"><?php echo $contribute->user->getFullName(); ?></a>
											<a class="link-muted" href="<?php echo base\url("contribute/{$contribute->id}"); ?>"><?php echo $contribute->title; ?></a>
										</div>
									</div>
								</div>
							</div>
							<div class="contribute-container">
								<div class="row">
									<div class="col-sm-11 col-sm-offset-1">
										<div class="panel panel-default">
											<div class="panel-body">
												<span class="badge"><?php echo $contribute->getPoint(); ?></span>
												<div class="row">
													<div class="col-sm-1 col-xs-2">
														<a href="<?php echo base\url("contribute/{$contribute->id}"); ?>">
															<img src="<?php echo $contribute->getImage(32, 32); ?>" alt="<?php echo $contribute->title; ?>">
														</a>
													</div>
													<div class="col-sm-11 col-xs-10">
														<?php echo $contribute->getPreviewContent(); ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
					<div class="row">
						<div class="col-xs-12">
							<button type="button" class="btn btn-default btn-block btn-load-more" data-user="<?php echo $this->user->id; ?>">بیشتر</button>
						</div>
					</div>
				<?php } else { ?>
					<div class="row">
						<div class="col-xs-12">
							<div class="alert alert-warning text-center" role="alert">
								<p>بدون فعالیت</p>
							</div>
						</div>
					</div>
				<?php } ?> 
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
