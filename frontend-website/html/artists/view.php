<?php
use \packages\base;
use \packages\base\{translator, frontend\theme};
use \packages\userpanel\date;
use \packages\ghafiye\song\person;
$this->the_header('artists');
$lang = $this->getSongLanguage();
?>
<div class="row main-row">
	<section class="songs col-sm-8">
		<h3><?php echo translator::trans('lyrics.top.byArtist', array('artist' => $this->artist->name($lang))); ?></h3>
		<?php if ($songs = $this->getSongs()) { ?>
		<ul>
			<?php
			$x = 0;
			foreach($songs as $song){
				$singer = $song->getPerson(person::singer);
			?>
			<li class="row">
				<div class="col-sm-1 col-xs-2">
					<span><?php echo ++$x; ?></span>
				</div>
				<div class="col-sm-1 col-xs-2">
					<img <?php echo !$song->image ? 'class="default"' : ''; ?> src="<?php echo $song->getImage(32, 32); ?>" alt="<?php echo $song->title($lang); ?>">
				</div>
				<div class="col-sm-10 col-xs-8">
					<a href="<?php echo(base\url($singer->encodedName($lang).'/'.$song->encodedTitle($lang))); ?>"><strong><?php echo $song->title($lang); ?></strong></a>
					<a href="<?php echo(base\url($singer->encodedName($lang))); ?>"><?php if($singer)echo $singer->name($lang); ?></a>
				</div>
			</li>
			<?php } ?>
		</ul>
		<?php } else { ?>
			<div class="alert alert-warning">
				<p>هنوز آهنگی برای این خواننده اضافه نشده است</p>
			</div>
		<?php } ?>
	</section>
	<aside class="col-sm-4">
		<?php if ($albums = $this->getAlbums()) { ?>
		<div class="panel panel-albums">
			<div class="panel-heading">
				<?php echo translator::trans('artist.albums'); ?>
				<a class="pull-left" href="<?php echo base\url($this->artist->encodedName($lang)."/albums"); ?>"><?php echo translator::trans('artist.albums.more'); ?> <i class="fa fa-angle-left"></i></a>
			</div>
			<div class="panel-body">
				<ul>
  	  			<?php
	  	  			foreach($albums as $album){
	  	  			?>
	  	  			<li>
	  	  				<img src="<?php echo $album->getImage(42, 42); ?>" alt="<?php echo $album->title($lang); ?>">
	  	  				<div>
	  	  					<a href="<?php echo(base\url($this->artist->encodedName($lang).'/albums/'.$album->encodedTitle($lang))); ?>"><strong><?php echo $album->title($lang); ?></strong></a>
	  	  					<span><?php echo date::format('Y', $this->getAlbumReleaseDate($album)); ?></span>
	  	  				</div>
	  	  			</li>
	  	  			<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-xs-12">
				<a class="banner-ad" href="https://www.jeyserver.com" target="_blank" title="هاست لینوکس، برنامه نویسی php">
					<img src="<?php echo theme::url("assets/images/ads/3078323516528828115111121661694.gif"); ?>" alt="جی هاست لینوکس، برنامه نویسی php">
				</a>
			</div>
		</div>
	</aside>
</div>
<?php
$this->the_footer();
