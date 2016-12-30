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
	<div class="col-md-5 col-lg-4">
		<img src="<?php echo $this->albumImage(); ?>" class="album-image">
	</div>
	<div class="col-md-7 col-lg-8 album">
		<h1><?php echo $this->album->title($lang); ?></h1>
		<h2><a href="<?php echo(base\url($this->artist->encodedName($lang))); ?>"><?php echo $this->artist->name($lang); ?></a></h2>
		<span><?php echo date::format('Y', $this->getAlbumReleaseDate()); ?> - <?php echo translator::trans('album.songs.byNumber', array('number' => count($this->getSongs()))); ?></span>
		<ul>
			<?php
			$x=0;
			foreach($this->getSongs() as $song){
				if($song->status == song::publish){
			?>
			<li><a href="<?php echo(base\url($this->artist->encodedName($lang).'/'.$song->encodedTitle($lang))); ?>">
				<span><?php echo ++$x; ?></span>
				<strong><?php echo $song->title($lang); ?></strong>
			</li></li>

			<?php
				}
			}
			?>
		</ul>
	</div>
</div>
<?php
$this->the_footer();
