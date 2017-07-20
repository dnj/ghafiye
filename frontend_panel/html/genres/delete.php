<?php
use \packages\base\translator;
use \packages\userpanel;
$this->the_header();
$genre = $this->getGenre();
?>
<div class="row">
	<div class="col-sm-12">
		<form action="<?php echo userpanel\url('genres/delete/'.$genre->id); ?>" method="POST">
			<div class="alert alert-block alert-warning fade in">
				<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> <?php echo translator::trans("attention"); ?>!</h4>
				<p>
					<?php echo translator::trans("ghafiye.genre.delete.warning", array("genre_id" => $genre->id)); ?>
				</p>
				<p>
					<a href="<?php echo userpanel\url('genres'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('return'); ?></a>
					<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o tip"></i> <?php echo translator::trans("ghafiye.delete") ?></button>
				</p>
			</div>
		</form>
	</div>
</div>
<?php
$this->the_footer();
