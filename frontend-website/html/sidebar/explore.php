<?php
use \packages\base;
use \packages\base\translator;
use \themes\musixmatch\views\explore\best;
use \themes\musixmatch\views\explore\lastest;
use \themes\musixmatch\views\explore\genre;
?>
 <div class="col-md-3 sidebar">
	 <div class="list-group">
		 <a href="<?php echo base\url('explore'); ?>" class="list-group-item<?php if($this instanceof best)echo(' active'); ?>"><i class="fa fa-free-code-camp"></i> <?php echo translator::trans('explore.best'); ?></a>
		 <a href="<?php echo base\url('explore/lastest'); ?>" class="list-group-item<?php if($this instanceof lastest)echo(' active'); ?>"> <?php echo translator::trans('explore.lastest'); ?></a>
	 </div>
	 <div class="panel panel-default">
		 <div class="panel-heading"><?php echo translator::trans('explore.genres'); ?></div>
		 <ul class="list-group">
			 <?php
			 foreach($this->getGenres(10) as $genre){
				 $active = ($this instanceof genre and $this->getGenre()->id == $genre->id);
			 ?>
				 <a href="<?php echo base\url('explore/genre/'.$genre->encodedTitle()); ?>" class="list-group-item<?php if($active)echo(' active'); ?>"><?php echo $genre->title(); ?></a>
			 <?php } ?>
		 </ul>
	 </div>
 </div>
