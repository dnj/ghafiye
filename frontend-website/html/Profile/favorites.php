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
				<a href="<?php echo base\url("profile/{$this->user->id}"); ?>">
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
				</a>
			</div>
			<div class="col-sm-7">
				<?php
				if ($this->favorites) {
					$i = 0;
					foreach ($this->favorites as $song) {
						$singer = $song->getPerson(person::singer);
						if ($i % 3 == 0) {
				?>
					<div class="row">
					<?php } ?>
					<div class="col-sm-4">
						<a target="_blank" href="<?php echo base\url($singer->encodedName().'/'.$song->encodedTitle()); ?>">
							<div class="panel">
								<div class="panel-body">
									<div class="row">
										<div class="form-group">
											<img src="<?php echo $song->getImage(195, 195); ?>" alt="<?php echo $song->title(); ?>" class="img-responsive img-thumbnail">
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12">
											<p class="song-title"><?php echo $song->title(); ?></p>
											<p class="singer-name"><?php echo $singer->name(); ?></p>
										</div>
									</div>
								</div>
							</div>
						</a>
					</div>
					<?php
					$i++;
					if ($i % 3 == 0) {
					?>
					</div>
				<?php
						}
					}
					$i++;
					if ($i % 3 == 0) {
					?>
					</div>
				<?php
					}
				} else {

				}
				?>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
