<?php
use \packages\base;
use \packages\base\translator;
use \packages\blog\post;
$this->the_header();
?>
<span class="errorcode">404</span>
<h1><?php echo translator::trans('notfound.title'); ?></h1>
<p class="errordescription"><?php echo translator::trans('blog.notfound.description', ['blog.home'=>base\url('blog')]); ?></p>
<div class="row">
	<form action="<?php echo base\url('blog/search'); ?>" method="GET">
	<div class="col-sm-6 col-sm-offset-3">
		<?php $this->createField([
			'name' => 'word',
			'input-group' => [
				'right' => [
					[
						'text' => translator::trans('blog.search'),
						'icon' => 'fa fa-search',
						'type' => 'submit',
						'class' => ['btn', 'btn-sm', 'btn-warning']
					]
				]
			]
		]); ?>
	</div>
	</form>
</div>
<?php $this->the_footer(); ?>
