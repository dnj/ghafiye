<?php
use packages\base;
use packages\base\{date, packages};
use packages\ghafiye\song\person;
$this->the_header();
?>
<div class="row">
	<div class="col-sm-8">
		<div class="alert alert-warning">
			<button data-dismiss="alert" class="close" type="button">&times;</button>
			<h4 class="alert-heading"><i class="fa fa-headphones"></i> به جامعه قافیه خوش آمدید </h4>
			<p class="text-muted">متن آهنگ ها را با دیگران به اشتراک بگذارید، ترجمه کنید و این لذت را با دیگران سهیم شوید.</p>
			<a href="<?php echo base\url("contribute");  ?>" class="btn btn-primary">مشارکت در لیگ قافیه</a>
		</div>
		<div class="contributes">
		<?php
		$contributes = $this->getContributes();
		if ($contributes) {
			foreach ($contributes as $contribute) {
		?>
			<div class="row">
				<div class="col-xs-12">
					<div class="contribute-info">
						<time><?php echo date::relativeTime($contribute->done_at); ?></time>
						<div class="row">
							<div class="col-sm-1 col-xs-2">
								<div class="contributor-avatar">
									<a href="<?php echo base\url("profile/{$contribute->user->id}"); ?>">
										<img src="<?php echo $contribute->user->getAvatar(32, 32); ?>" class="img-responsive img-circle" ‌title="مشاهده پروفایل">
									</a>
								</div>
							</div>
							<div class="col-sm-10 col-xs-7">
								<div class="contributor-name">
									<a href="<?php echo base\url("profile/{$contribute->user->id}"); ?>"><?php echo $contribute->user->getFullName(); ?></a>
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
		<?php } ?>
		</div>
	</div>
	<div class="col-sm-4">
	<?php if ($this->users) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				مشارکت دهندگان برتر هفته
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body contributors">
				<ul class="list-group">
					<?php
					$length = count($this->users);
					for ($i = 0; $i < $length; $i++) {
						$user = $this->users[$i]->user;
						$level = $i + 1;
					?>
					<li class="list-group-item">
						<a href="<?php echo base\url("profile/{$user->id}"); ?>">
							<span class="badge<?php echo $i < 3 ? " badge-{$level}" : ""; ?>"><?php echo $level; ?></span>
							<div class="user-avatar">
								<img src="<?php echo $user->getAvatar(32, 32); ?>" class="img-responsive img-circle tooltips" ‌title="مشاهده پروفایل">
							</div>
							<div class="user-name">
								<p><?php echo $user->getFullName(); ?></p>
								<small><?php echo $this->users[$i]->cpoints; ?> امتیاز</small>
							</div>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
<?php
$this->the_footer();
