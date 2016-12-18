<?php
use \packages\base;
use \packages\base\translator;
?>
<footer>
	<div class="container">
		<div class="row">
			<div class="col-md-3"></div>
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
					<li><a href="https://telegram.me/ghafiye_com"><i class="fa fa-telegram"></i> <?php echo translator::trans('footer.link.telegram'); ?></a></li>
					<li><a href="<?php echo base\url('instagram'); ?>"><i class="fa fa-instagram"></i> <?php echo translator::trans('footer.link.instagram'); ?></a></li>
					<li><a href="<?php echo base\url('facebook'); ?>"><i class="fa fa-facebook"></i> <?php echo translator::trans('footer.link.facebook'); ?></a></li>
					<li><a href="<?php echo base\url('twitter'); ?>"><i class="fa fa-twitter"></i> <?php echo translator::trans('footer.link.twitter'); ?></a></li>
				</ul>
			</div>
		</div>
		<div class="note">
			<p><?php echo translator::trans('footer.notelove'); ?></p>
			<div class="copyright"><?php echo translator::trans('footer.copyright'); ?></p>
		</div>
	</div>
</footer>
<?php $this->loadJS(); ?>
</body>
</html>
