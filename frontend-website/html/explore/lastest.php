<?php
use \packages\base;
use \packages\base\translator;
use \packages\ghafiye\song\person;
$this->the_header();
?>
<div class="row">
	<?php $this->the_sidebar('explore'); ?>
	<div class="col-md-9">
	<section class=" songs">
		<h2><?php echo translator::trans('explore.lastest.title'); ?></h2>
		<p><?php echo translator::trans('explore.lastest.description'); ?></p>
		<ul>
			<?php
			$x=0;
			foreach($this->getSongs() as $song){
				$singer = $song->getPerson(person::singer);
			?>
			<li>
				<span><?php echo ++$x; ?></span>
				<img src="<?php echo $this->songImage($song); ?>" alt="<?php echo $song->title(); ?>">
				<div>
					<a href="<?php echo(base\url($singer->encodedName().'/'.$song->encodedTitle())); ?>"><strong><?php echo $song->title(); ?></strong></a>
					<a href="<?php echo(base\url($singer->encodedName())); ?>"><?php if($singer)echo $singer->name(); ?></a>
				</div>
			</li>
			<?php
			}
			?>
		</ul>
		<?php echo $this->pager(); ?>
	</section>
</div>
</div>
<?php
$this->the_footer();
