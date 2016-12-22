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
		<header class="row">
			<div class="col-xs-5">
				<img src="<?php echo($this->blogImage($this->post)) ?>" class="song-image">
			</div>
			<div class="col-xs-7 title">
				<h1><?php echo $this->post->title; ?></h1>
				<h2><a href="<?php echo(base\url("blog/author/".$this->post->author->id)); ?>"><?php echo translator::trans("blog.post.wrightby", array("author" => $this->post->author->getFullName())); ?></a></h2>
				<div class="translations">
					<span><?php echo translator::trans('blog.post.sendin', array('date' => date::format("F Y", $this->post->date))); ?></span>
					<h5><?php if($this->showCategories())echo translator::trans('blog.post.sendin').$this->showCategories(); ?></h5>
				</div>
			</div>
		</header>
		<div class="row space">
			<section class="col-sm-11 col-md-11 text col-md-offset-1">
				<?php echo $this->post->content; ?>
			</section>
		</div>
		<div class="row">
			<h1 class="title"><?php echo translator::trans("blog.post.tags"); ?></h1>
			<section class="col-sm-11 col-md-11 text col-md-offset-1">
				<?php foreach($this->getTags() as $tag){ ?>
					<a href="<?php echo base\url("blog/tag/".$tag->title); ?>"><span><?php echo $tag->title; ?></span></a>
					<?php } ?>
				</section>
			</div>
			<hr>
			<?php if(count($this->post->comments)){ ?>
				<div class="row">
					<h1 class="title"><?php echo translator::trans("blog.post.comments"); ?></h1>
					<div class="feed-panel">
						<div class="activity-feed">
							<ul class="activities list">
								<?php echo $this->revertReply(); ?>
							</ul>
						</div>
					</div>
				</div>
				<hr>
				<?php } ?>
				<div <?php if($this->getData("status") and http::is_post())echo('id="success"'); ?> class="row setcomment">
					<h1 class="title"><?php echo translator::trans("blog.post.setcomment"); ?></h1>
					<div class="col-md-12 reply-info" style="display: none;">
						<div class="col-md-7">
							<p class="reply-info-name"></p>
						</div>
						<div class="col-md-3">
							<button type="button" class="btn btn-info cancel-reply"><?php echo translator::trans("blog.post.cancel.comment.reply"); ?></button>
						</div>
					</div>
					<form  action="<?php echo base\url("blog/view/".$this->post->id); ?>" method="post">
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
