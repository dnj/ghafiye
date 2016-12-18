<?php
use \packages\base;
use \packages\base\http;
use \packages\base\translator;
use \packages\base\frontend\theme;
require_once("header.php"); ?>
        <div class="pageTitleArea animated" id="particles-js">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="pageTitle">
                            <ul class="pageIndicate">
                                <li><a href="#">home</a></li>
                                <li><a href="#">contact</a></li>
                            </ul>
                            <div class="h2">contact us</div>
                            <span class="pageTitleBar"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- ** end heroArea **  -->

<!-- ** start Location Area **  -->
	<div class="locationArea sp90 animated">
		<div class="container">
			<div class="row">
				<div class="col-md-3 col-md-offset-1">
					<div class="singleLocation">
						<div class="locIcon">
							<img src="<?php echo theme::url("img/icons/locIcon01.png"); ?>" alt="">
						</div>
						<span class="locContent">031-34420301</span>
					</div>
				</div>
				<div class="col-md-4">
					<div class="singleLocation">
						<div class="locIcon">
							<img src="<?php echo theme::url("img/icons/locIcon02.png"); ?>" alt="">
						</div>
						<span class="locContent">info@ghafiye.com</span>
						<span class="locContent">support@ghafiye.com</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="singleLocation">
						<div class="locIcon">
							<img src="<?php echo theme::url("img/icons/locIcon03.png"); ?>" alt="">
						</div>
						<span class="locContent">اصفهان - اصفهان</span>
						<span class="locContent">خیابان نشاط ساختمان شمشاد</span>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- ** end Location Area **  -->

<!-- ** start GoogleMap **  -->
	<div class="mapArea animated">
		<div id="googleMap"></div>
		<form action="<?php echo base\url("contact"); ?>" method="post" class="contactForm">
			<div class="h3 mapTitle">فرم تماس با ما</div>
			<?php
			$fields = array(
				array(
					'name' => 'name',
					'class' => 'form-control w-input text-field',
					'placeholder' => translator::trans('contact.form.input.name')
				),
				array(
					'type' => 'email',
					'name' => 'email',
					'class' => 'form-control w-input text-field',
					'placeholder' => translator::trans('contact.form.input.email'),
					'error' => array(
						'data_duplicate' => 'user.email.data_duplicate'
					)
				),
				array(
					'name' => 'subject',
					'class' => 'form-control w-input text-field',
					'placeholder' => translator::trans('contact.form.input.subject')
				),
				array(
					'type' => 'textarea',
					'name' => 'text',
					'class' => 'form-control w-input text-field',
					'placeholder' => translator::trans('contact.form.input.text'),
					'error' => array(
						'data_duplicate' => 'user.cellphone.data_duplicate'
					)
				)
			);
			foreach($fields as $field){
				$this->createField($field);
			}
			?>
			<input type="submit" value="ارسال">
            <div class="formMsgWrep">
                <div id="form-messages"></div>
            </div>
		</form>
	</div>
<!-- ** end GoogleMap **  -->

<?php require_once("footer.php");
