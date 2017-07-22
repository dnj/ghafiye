<?php
use \packages\base;
use \packages\base\http;
use \packages\base\translator;
use \packages\userpanel\date;
$this->the_header();
?>
<div class="row">
	<?php $this->the_sidebar('blog'); ?>
	<div class="col-md-9 container">
		<section class="posts">
			<article class="post">
				<div class="row">
					<div class="col-md-12">
						<header>
							<h2><?php echo($this->post->title); ?></h2>
							<p><?php echo translator::trans("blog.post.sendin", array("category" => $this->showCategories())); ?></p>
							<ul class="post-meta">
								<li><i class="fa fa-clock-o"></i><?php echo date::format("l، j F", $this->post->date) ?></li>
								<li><i class="fa fa-eye"></i><?php echo translator::trans("blog.post.view.number", array("view" => $this->post->view)); ?></li>
								<li><i class="fa fa-user"></i><a href="<?php echo base\url("blog/author/".$this->post->author->id); ?>" class="artist"><?php echo $this->post->author->getFullName(); ?></a></li>
								<li><i class="fa fa-comments"></i> <?php echo translator::trans("blog.post.comments.number", array("count" => $this->post->getCountPostCommnets())); ?></li>
							</ul>
						</header>
						<div class="post-content">
							<?php echo $this->post->content; ?>
						</div>
						<?php if($this->getTags()){ ?>
						<div class="row tags">
							<h3 class="title"><?php echo translator::trans("blog.post.tags"); ?></h3>
							<section class="col-sm-11 col-md-11">
								<?php foreach($this->getTags() as $tag){ ?>
								<a class="btn btn-default" href="<?php echo base\url("blog/tag/".$tag->title); ?>"><span><?php echo $tag->title; ?></span></a>
								<?php } ?>
							</section>
						</div>
						<?php } ?>
					</div>
				</div>
			</article>
		</section>
		<?php if(count($this->post->comments)){ ?>
			<div class="row comments">
				<h1 class="title"><?php echo translator::trans("blog.post.comments"); ?></h1>
				<section class="posts">
				<?php echo $this->revertReply(); ?>
				</section>
			</div>
			<?php } ?>
			<div <?php if($this->getData("status") and http::is_post())echo('id="success"'); ?> class="row setcomment">
				<h1><?php echo translator::trans("blog.post.setcomment"); ?></h1>
				<div class="col-md-12 reply-info" style="display: none;">
					<div class="col-md-10">
						<p class="reply-info-name"></p>
					</div>
					<div class="col-md-2">
						<button type="button" class="btn btn-info btn-block cancel-reply"><?php echo translator::trans("blog.post.cancel.comment.reply"); ?></button>
					</div>
				</div>
				<form  action="<?php echo $this->post->getURL(); ?>" method="post">
					<?php $this->createField(array(
						'name' => "reply",
						'type' => 'hidden'
					));
					?>
					<div class="col-md-6">
						<?php $this->createField(array(
							'name' => "name",
							'placeholder' => translator::trans("blog.post.comment.name")
						));
						?>
					</div>
					<div class="col-md-6">
						<?php $this->createField(array(
							'name' => "email",
							'type' => "email",
							'placeholder' => translator::trans("blog.post.comment.email")
						));
						?>
					</div>
					<div class="col-md-12">
						<?php $this->createField(array(
							'name' => "text",
							'type' => "textarea",
							'rows' => 8,
							'placeholder' => translator::trans("blog.post.comment.text")
						));
						?>
					</div>
					<div class="col-md-12">
						<button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("blog.post.comment.send"); ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php
	$this->the_footer();
