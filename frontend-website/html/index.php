<?php
use \packages\base;
use \packages\base\frontend\theme;
use \packages\base\translator;
use \packages\ghafiye\song\person;
$this->the_header('home');
?>
<div class="container">
	<section class="toplyrics">
		<h2><?php echo translator::trans('home.section.toplyrics.title'); ?></h2>
		<?php $genres = $this->getGenres(); ?>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#genre-all" aria-controls="genre-all" role="tab" data-toggle="tab"><?php echo translator::trans('home.section.toplyrics.allgenres'); ?></a></li>
			<?php foreach($genres as $genre){ ?>
			<li role="presentation"><a href="#genre-<?php echo $genre->encodedTitle('en'); ?>" aria-controls="genre-<?php echo $genre->encodedTitle('en'); ?>" role="tab" data-toggle="tab"><?php echo $genre->title(); ?></a></li>
			<?php } ?>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="genre-all">
				<div class="row">
				<?php
				foreach($this->getTopSongs() as $song){
					$singer = $song->getPerson(person::singer);
				?>
				<div class="col-md-4 col-sm-6">
					<a class="song" href="<?php echo(base\url($singer->encodedName().'/'.$song->encodedTitle())); ?>">
						<div class="image" style="background-image: url(<?php echo $song->getImage(347, 260); ?>);">

						</div>
						<div class="description">
							<ul class="info pull-right">
								<li class="title"><?php echo $song->title(); ?></li>
								<li class="artist"><?php if($singer)echo $singer->name(); ?></li>
							</ul>
							<ul class="buttons pull-left">
								<li class="likes"><i class="fa fa-heart-o"></i> <?php echo $song->likes; ?></li>
								<li class="languages"><i class="fa fa-language"></i> <?php echo $this->numberOfLangs($song); ?></li>
							</ul>
						</div>
					</a>
				</div>
				<?php } ?>
				</div>
			</div>
			<?php foreach($genres as $genre){ ?>
			<div role="tabpanel" class="tab-pane" id="genre-<?php echo $genre->encodedTitle('en'); ?>">
				<div class="row">
				<?php
				foreach($this->getTopSongsByGenre($genre) as $song){
					$singer = $song->getPerson(person::singer);
				?>
					<div class="col-md-4 col-sm-6">
						<a class="song" href="<?php echo(base\url($singer->encodedName().'/'.$song->encodedTitle())); ?>">
							<div class="image" style="background-image: url(<?php echo $song->getImage(347, 260); ?>);">

							</div>
							<div class="description">
								<ul class="info pull-right">
									<li class="title"><?php echo $song->title(); ?></li>
									<li class="artist"><?php echo $singer->name(); ?></li>
								</ul>
								<ul class="buttons pull-left">
									<li class="likes"><i class="fa fa-heart-o"></i> <?php echo $song->likes; ?></li>
									<li class="languages"><i class="fa fa-language"></i> <?php echo $this->numberOfLangs($song); ?></li>
								</ul>
							</div>
						</a>
					</div>
				<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
		<a href="<?php echo base\url('explore'); ?>" class="more"><?php echo translator::trans('home.section.toplyrics.more'); ?></a>
	</section>
	<section class="lastlyrics">
		<h2><?php echo translator::trans('home.section.lastlyrics.title'); ?></h2>

		<div class="row">
		<?php
		foreach($this->getLastSongs() as $song){
			$singer = $song->getPerson(person::singer);
		?>
		<div class="col-md-3 col-sm-6">
			<a class="song" href="<?php echo(base\url($singer->encodedName().'/'.$song->encodedTitle())); ?>">
				<div class="image" style="background-image: url(<?php echo $song->getImage(263, 260); ?>);">

				</div>
				<div class="description">
					<ul class="info pull-right">
						<li class="title"><?php echo $song->title(); ?></li>
						<li class="artist"><?php if($singer)echo $singer->name(); ?></li>
					</ul>
				</div>
			</a>
		</div>
		<?php } ?>
		</div>
		<a href="<?php echo base\url('explore/lastest'); ?>" class="more"><?php echo translator::trans('home.section.toplyrics.more'); ?></a>
	</section>
</div>
<?php $this->the_footer('home'); ?>
