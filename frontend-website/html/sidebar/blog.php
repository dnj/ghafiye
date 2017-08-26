<?php
use \packages\blog;
use \packages\base\translator;
use \packages\userpanel\date;
?>
 <div class="col-md-3 sidebar">
	 <?php if($subjects = $this->getPostsSubJects()){ ?>
	 <div class="panel panel-default">
		<div class="panel-heading"><?php echo translator::trans('blog.sidebar.subjects'); ?></div>
		<div class="panel-body">
			<ul class="categories">
				<?php echo $this->getPostsSubJects(); ?>
			</ul>
		</div>
	</div>
	<?php } ?>
	<?php if($popularsPost = $this->popular_blog()){ ?>
	 <div class="panel panel-default">
		<div class="panel-heading"><?php echo translator::trans('blog.popular_blog'); ?></div>
		<ul class="list-group">
			<?php foreach($popularsPost as $popular){ ?>
				<li><a href="<?php echo $popular->getUrl(); ?>" class="list-group-item"><?php echo($popular->title); ?></a></li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
	<?php if($archivesPosts = $this->archive_box()){ ?>
	 <div class="panel panel-default">
		 <div class="panel-heading"><?php echo translator::trans('blog.archive_box'); ?></div>
		 <ul class="list-group">
			 <?php foreach($archivesPosts as $archive){ ?>
				 <li><a href="<?php echo blog\url("archive/".date::format("Y/m", $archive)); ?>" class="list-group-item"><?php echo(date::format("F Y", $archive)); ?></a></li>
			 <?php } ?>
		 </ul>
	 </div>
	<?php } ?>
	 <div class="panel panel-default">
		<div class="panel-heading"><?php echo translator::trans('blog.search'); ?></div>
		<div class="panel-body">
			<form action="<?php echo blog\url('search'); ?>" method="GET">
				<?php $this->createField([
					'name' => 'word',
					'input-group' => [
						'right' => [
							[
								'text' => '',
								'icon' => 'fa fa-search',
								'type' => 'submit',
								'class' => ['btn', 'btn-sm', 'btn-teal']
							]
						]
					]
				]); ?>
			</form>
		</div>
	 </div>
 </div>
