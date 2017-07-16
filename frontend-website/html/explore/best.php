<?php
use \packages\base;
use \packages\base\translator;
use \packages\ghafiye\song\person;
$this->the_header();
?>
<div class="row">
	<div class="col-md-9 col-md-push-3">
		<section class="songs">
			<h2><?php echo translator::trans('explore.best.title'); ?></h2>
			<p><?php echo translator::trans('explore.best.description'); ?></p>
			<ul>
				<?php
				$x=0;
				foreach($this->getSongs() as $song){
					$singer = $song->getPerson(person::singer);
				?>
				<li class="row">
					<div class="col-sm-1 col-xs-2">
						<span><?php echo ++$x; ?></span>
					</div>
					<div class="col-sm-1 col-xs-2">
						<img src="<?php echo $this->songImage($song); ?>" alt="<?php echo $song->title(); ?>">
					</div>
					<div class="col-sm-10 col-xs-8">
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
	<?php $this->the_sidebar('explore'); ?>
</div>
<?php
$this->the_footer();
