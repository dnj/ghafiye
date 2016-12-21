<?php
use \packages\base;
use \packages\base\translator;
use \packages\userpanel\date;
?>
 <div class="col-md-3 sidebar">
	 <div class="panel panel-default">
		<div class="panel-heading"><?php echo translator::trans('blog.popular_blog'); ?></div>
		<ul class="list-group">
			<?php foreach($this->popular_blog() as $popular){ ?>
				<a href="<?php echo base\url("blog/view/".$popular->id); ?>" class="list-group-item"><?php echo($popular->title); ?></a>
			<?php } ?>
		</ul>
	</div>
	 <div class="panel panel-default">
		 <div class="panel-heading"><?php echo translator::trans('blog.archive_box'); ?></div>
		 <ul class="list-group">
			 <?php foreach($this->archive_box() as $archive){ ?>
				 <a href="<?php echo base\url("blog/archive/".date::format("Y/m", $archive)); ?>" class="list-group-item"><?php echo(date::format("F Y", $archive)); ?></a>
			 <?php } ?>
		 </ul>
	 </div>
 </div>
