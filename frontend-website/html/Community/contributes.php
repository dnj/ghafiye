<?php
use packages\base;
use packages\base\{date, packages};
use packages\ghafiye\song\person;
$this->the_header();
?>
<div class="row">
	<div class="col-sm-8">
		<div class="alert alert-info">
			<button data-dismiss="alert" class="close" type="button">&times;</button>
			<h4 class="alert-heading"><i class="fa fa-headphones"></i> به جامعه آماری قافیه خوش آمدید </h4>
			<p class="text-muted">شما آثار و اشخاص را معرفی می کنید، شما ترانه ها را ترجمه می کنید و در نهایت خودتان از آن ها استفاده می کنید.</p>
			<a href="<?php echo base\url("contribute");  ?>" class="btn btn-primary">مشارکت</a>
		</div>
		<div class="contributes">
		<?php
		$contributes = $this->getContributes();
		if ($contributes) {
			foreach ($contributes as $contribute) {
				$handler = $contribute->getHandler();
		?>
			<div class="row">
				<div class="col-xs-12">
					<div class="contribute-info">
						<time><?php echo date::relativeTime($contribute->done_at); ?></time>
						<div class="row">
							<div class="col-sm-1 col-xs-2">
								<div class="contributor-avatar">
									<a href="#">
										<img src="<?php echo $contribute->user->getAvatar(32, 32); ?>" class="img-responsive img-circle" ‌title="مشاهده پروفایل">
									</a>
								</div>
							</div>
							<div class="col-sm-11 col-xs-8">
								<div class="contributor-name">
									<a href="#"><?php echo $contribute->user->getFullName(); ?></a>
									<span><?php echo $contribute->title; ?></span>
								</div>
							</div>
						</div>
					</div>
					<div class="contribute-container">
						<div class="row">
							<div class="col-sm-11 col-sm-offset-1">
								<div class="panel panel-default">
									<div class="panel-body">
										<div class="row">
											<div class="col-sm-1 col-xs-2">
												<img src="<?php echo $contribute->song->getImage(32, 32); ?>" alt="<?php echo $contribute->song->title(); ?>">
											</div>
											<div class="col-sm-11 col-xs-10">
												<?php $singer = $contribute->song->getPerson(person::singer); ?>
												<p><a href="<?php echo base\url($singer->encodedName().'/'.$contribute->song->encodedTitle()); ?>"><?php echo $contribute->song->title(); ?></a></p>
												<p><a href="<?php echo base\url($singer->encodedName()); ?>" class="song-singer"><?php echo $singer->name(); ?></a></p>
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
				<div class="alert alert-warning" role="alert">
					<p>بدون فعالیت</p>
				</div>
			</div>
		</div>
		<?php } ?>
		</div>
	</div>
	<div class="col-sm-4">
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
						$user = $this->users[$i];
						$level = $i + 1;
					?>
					<li class="list-group-item">
						<a href="<?php echo base\url("profile/{$user->id}"); ?>">
							<span class="badge<?php echo $i < 3 ? " badge-{$level}" : ""; ?>"><?php echo $level; ?></span>
							<div class="user-avatar">
								<img src="<?php echo $user->getAvatar(32, 32); ?>" class="img-responsive img-circle" ‌title="مشاهده پروفایل">
							</div>
							<div class="user-name">
								<p><?php echo $user->getFullName(); ?></p>
								<small><?php echo $user->points; ?> امتیاز</small>
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
