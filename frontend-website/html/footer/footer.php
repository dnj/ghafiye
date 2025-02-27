<?php
use \packages\base;
use \packages\base\http;
use \packages\geoip\api as geoip;
use \packages\base\translator;
use \packages\base\frontend\theme;
?>
</main>
<footer>
	<div class="container">
		<div class="row">
			<div class="col-md-3 text-center">
				<?php
				$geoip = new geoip();
				$country_code = $geoip->country_code_by_addr(http::$client['ip']);
				if($country_code and $country_code != 'IR'){
				?>
				<img alt="<?php echo translator::trans("ghafiye.homepage.title"); ?>" src="<?php echo theme::url('assets/images/android-chrome-192x192.png'); ?>" title="<?php echo translator::trans("ghafiye.homepage.title"); ?>" />
				<?php }else{ ?>
				<img id='wlaofukznbqejxlzjzpe' style='cursor:pointer' onclick='window.open("https://logo.samandehi.ir/Verify.aspx?id=46217&p=aodsgvkauiwkrfthjyoe", "Popup","toolbar=no, scrollbars=no, location=no, statusbar=no, menubar=no, resizable=0, width=450, height=630, top=30")' alt='logo-samandehi' src='https://logo.samandehi.ir/logo.aspx?id=46217&p=shwlwlbqodrfnbpdyndt'/>
				<?php } ?>
			</div>
			<div class="col-md-3">
				<h4><?php echo translator::trans('footer.overview'); ?></h4>
				<ul>
					<li><a href="<?php echo base\url('blog'); ?>"><?php echo translator::trans('footer.link.blog'); ?></a></li>
					<li><a href="<?php echo base\url('apps'); ?>"><?php echo translator::trans('footer.link.apps'); ?></a></li>
					<li><a href="<?php echo base\url('copyright'); ?>"><?php echo translator::trans('footer.link.copyright'); ?></a></li>
					<li><a href="<?php echo base\url('api'); ?>"><?php echo translator::trans('footer.link.api'); ?></a></li>
				</ul>
			</div>
			<div class="col-md-3">
				<h4><?php echo translator::trans('footer.company'); ?></h4>
				<ul>
					<li><a href="<?php echo base\url('about-us'); ?>"><?php echo translator::trans('footer.link.about-us'); ?></a></li>
					<li><a href="<?php echo base\url('jobs'); ?>"><?php echo translator::trans('footer.link.jobs'); ?></a></li>
					<li><a href="<?php echo base\url('advertising'); ?>"><?php echo translator::trans('footer.link.advertising'); ?></a></li>
					<li><a href="<?php echo base\url('contact-us'); ?>"><?php echo translator::trans('footer.link.contact-us'); ?></a></li>
				</ul>
			</div>
			<div class="col-md-3">
				<h4><?php echo translator::trans('footer.socialnetworks'); ?></h4>
				<ul class="socialnetworks">
					<li><a href="https://telegram.me/ghafiyecom"><i class="fa fa-telegram"></i> <?php echo translator::trans('footer.link.telegram'); ?></a></li>
					<li><a href="https://instagram.com/ghafiyecom"><i class="fa fa-instagram"></i> <?php echo translator::trans('footer.link.instagram'); ?></a></li>
					<li><a href="https://www.facebook.com/ghafiyecom"><i class="fa fa-facebook"></i> <?php echo translator::trans('footer.link.facebook'); ?></a></li>
					<li><a href="https://twitter.com/ghafiyecom"><i class="fa fa-twitter"></i> <?php echo translator::trans('footer.link.twitter'); ?></a></li>
				</ul>
			</div>
		</div>
		<div class="note">
			<div class="row">
				<div class="col-sm-9">
				<p><?php echo translator::trans('footer.notelove'); ?></p>
				<p class="copyright"><?php echo translator::trans('footer.copyright'); ?></p>
			</div>
			<p class="col-sm-3 text-left hidden-xs">میزبانی و برنامه نویسی: <a href="https://www.jeyserver.com" target="_blank" title="هاست لینوکس، برنامه نویسی php">جی سرور</a></p>
			<p class="col-sm-3 visible-xs-block">میزبانی و برنامه نویسی: <a href="https://www.jeyserver.com" target="_blank" title="هاست لینوکس، برنامه نویسی php">جی سرور</a></p>
		</div>
	</div>
</footer>
<?php $this->loadJS(); ?>
</body>
</html>
