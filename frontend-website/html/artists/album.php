<?php
use \packages\base;
use \packages\base\translator;
use \packages\userpanel\date;
use \packages\ghafiye\song;
use \packages\ghafiye\song\person;
$this->the_header();
$lang = $this->getSongLanguage();
?>
<div class="row">
	<div class="col-sm-5 col-lg-4">
		<img src="<?php echo $this->album->getImage(300, 350); ?>" class="album-image">
	</div>
	<div class="col-sm-7 col-lg-8 album">
		<h1><?php echo $this->album->title($lang); ?></h1>
		<h2><a href="<?php echo(base\url($this->artist->encodedName($lang))); ?>"><?php echo $this->artist->name($lang); ?></a></h2>
		<span><?php echo date::format('Y', $this->getAlbumReleaseDate()); ?> - <?php echo translator::trans('album.songs.byNumber', array('number' => count($this->getSongs()))); ?></span>
		<ul>
			<?php
			$x=0;
			foreach($this->getSongs() as $song){
				if($song->status == song::publish){
			?>
			<li>
				<a href="<?php echo(base\url($this->artist->encodedName($lang).'/'.$song->encodedTitle($lang))); ?>" class="row">
					<div class="col-sm-1 col-xs-2">
						<span><?php echo ++$x; ?></span>
					</div>
					<div class="col-sm-11 col-xs-10">
						<strong><?php echo $song->title($lang); ?></strong>
					</div>
				</a>
			</li>
			<?php
				}
			}
			?>
		</ul>
	</div>
</div>
<?php if(!empty($this->getMoreAlbums())){ ?>
<div class="row">
	<div class="col-sm-12">
		<div class="more-albums">
			<span class="more-albums-title"><?php echo translator::trans('more.albums.for', ['url'=>base\url($this->artist->encodedName($lang)), 'name'=>$this->artist->name($lang)]); ?></span>
			<div class="row">
				<?php foreach($this->getMoreAlbums() as $album){ ?>
				<div class="col-md-3 col-sm-6">
					<a class="album" href="<?php echo(base\url($this->artist->encodedName($lang).'/albums/'.$album->encodedTitle($lang))); ?>">
						<div class="image" style="background-image: url(<?php echo $album->getImage(263, 240); ?>);"></div>
						<div class="description">
							<ul class="info pull-right">
								<li class="title"><?php echo $album->title(); ?></li>
								<li class="year"><?php echo date::format('Y', $this->getAlbumReleaseDate($album)); ?></li>
							</ul>
						</div>
					</a>
				</div>
				<?php } ?>
			</div>
			<a href="<?php echo base\url($this->artist->encodedName($lang).'/albums'); ?>" class="more"><?php echo translator::trans('home.section.toplyrics.more'); ?></a>
		</div>
	</div>
</div>
<?php } ?>
<?php
$this->the_footer();
