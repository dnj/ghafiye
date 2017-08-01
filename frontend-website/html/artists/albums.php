<?php
use \packages\base;
use \packages\base\translator;
use \packages\userpanel\date;
use \packages\ghafiye\song\person;
$this->the_header('artists');
$lang = $this->getSongLanguage();
?>
<div class="row main-row">
	<?php
	foreach($this->getAlbums() as $album){
	?>
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
<?php
$this->the_footer();
