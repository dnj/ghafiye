<?php
use \packages\base;
use \packages\base\http;
use \packages\base\translator;
use \packages\base\frontend\theme;
$this->the_header();
?>
<article class="container">
	<h2>تماس با قافیه</h2>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<p>ما بسیار به قافیه علاقه داریم، همواره در تلاشیم که آن را بهبود ببخشیم و تجربه کاربری بهتری را برای شما به ارمغان بیاوریم \س لطفا ما را در این زمینه یاری کنید و اگر هر مشکل یا سوالی در خصوص این وب سایت داشتید با ما تماس بگیرید</p>
			
			<form class="form-horizontal" action="<?php echo base\url('contact-us'); ?>" method="post">
				<?php
				$fields = array(
					array(
						'name' => 'name',
						'label' => translator::trans('contact.form.name')
					),
					array(
						'type' => 'email',
						'name' => 'email',
						'label' => translator::trans('contact.form.email'),
						'error' => array(
							'data_duplicate' => 'user.email.data_duplicate'
						)
					),
					array(
						'name' => 'subject',
						'label' => translator::trans('contact.form.subject')
					),
					array(
						'type' => 'textarea',
						'name' => 'text',
						'label' => translator::trans('contact.form.text'),
					)
				);
				foreach($fields as $field){
					$this->createField($field);
				}
				?>
				<div class="col-sm-4 col-sm-offset-4">
					<button class="btn btn-success btn-block" type="submit"><?php echo translator::trans('contact.form.submit'); ?></button>
				</div>
			</form>
		</div>
	</div>
</article>
<?php $this->the_footer(); ?>
