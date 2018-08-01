<?php
use \packages\blog;
use \packages\base\translator;
use \packages\userpanel\date;
$this->the_header();
?>
<div class="row">
	<?php $this->the_sidebar('blog'); ?>
	<div class="col-md-9 container">
	<?php
	$errorcode = $this->getErrorsHTML();
	if($errorcode){
	?>
	<div class="row">
		<div class="col-xs-12"><?php echo $errorcode; ?></div>
	</div>
	<?php } ?>
		<?php if(!empty($this->getPosts())){ ?>
		<section class="posts">
			<?php foreach($this->getPosts() as $post){ ?>
				<article class="post">
					<div class="row">
						<div class="col-md-4">
							<img src="<?php echo $this->blogImage($post); ?>" alt="<?php echo $post->title; ?>" width="250px" height="250px">
						</div>
						<div class="col-md-8">
							<header>
								<h2><a href="<?php echo $post->getURL(); ?>"><?php echo($post->title); ?></a></h2>
								<ul class="post-meta">
									<li><i class="fa fa-clock-o"></i><a href="<?php echo blog\url('archive/'.date::format('Y/m', $post->date)); ?>"><?php echo date::format("l، j F Y", $post->date) ?></a></li>
									<li><i class="fa fa-eye"></i><?php echo translator::trans("blog.post.view.number", array("view" => $post->view)); ?></li>
									<li><i class="fa fa-user"></i><a href="<?php echo blog\url("author/".$post->author->id); ?>" class="artist"><?php echo $post->author->getFullName(); ?></a></li>
									<li><i class="fa fa-comments"></i> <?php echo translator::trans("blog.post.comments.number", array("count" => $post->getCountPostcomments())); ?></li>
								</ul>
							</header>
							<div class="post-content">
								<?php echo $post->firstPartContent(); ?>
							</div>
						</div>
					</div>
					<footer class="row">
						<div class="col-md-2 col-md-offset-10 buttons">
							<a href="<?php echo $post->getURL(); ?>" class="btn btn-success btn-block"><?php echo translator::trans("blog.post.continue"); ?></a>
						</div>
					</footer>
				</article>
				<?php
				}
				$this->paginator();
				?>
		</section>
		<?php } ?>
	</div>
</div>
<?php
$this->the_footer();
