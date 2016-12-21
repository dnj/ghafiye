<?php
use \packages\base;
use \packages\base\translator;
use \packages\ghafiye\song\person;
$this->the_header();
?>
<div class="row">
	<?php $this->the_sidebar('blog'); ?>
	<div class="col-md-9 container">
		<section class="toppost">
			<h2><?php echo translator::trans('blog.list.title'); ?></h2>
			<p><?php echo translator::trans('blog.list.description'); ?></p>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="genre-all">
						<div class="row">
							<?php
							foreach($this->getPosts() as $post){
								?>
							<div class="col-md-4 col-sm-6">
								<a class="post" href="<?php echo base\url('blog/view/'.$post->id); ?>">
									<div class="image" style="background-image: url(<?php echo $this->blogImage($post); ?>);"><span class="continue"><?php echo translator::trans("blog.continue.read.post") ?></span></div>
									<div class="description">
										<ul class="info pull-right">
											<li class="title"><?php echo($post->title); ?></li>
											<li class="artist"><?php echo($post->author->getFullName()); ?></li>
										</ul>
										<ul class="buttons pull-left">
											<li class="comment"><i class="fa fa-comment"></i> <?php echo(count($post->comments)); ?></li>
										</ul>
									</div>
								</a>
							</div>
							<?php
						}
						?>
						</div>
					</div>
				</div>
		</section>
	</div>
</div>
<?php
$this->the_footer();
