<?php
use \packages\base\translator;
use \packages\userpanel;
use \packages\ghafiye\person;
$this->the_header();
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2 swMain">
		<ul class="anchor">
			<li>
				<a class="selected" data-step="search">
					<div class="stepNumber"> 1 </div>
					<span class="stepDesc"> <?php echo translator::trans('ghafiye.panel.crawler.add.step.search.title'); ?>
						<br>
						<small><?php echo translator::trans('ghafiye.panel.crawler.add.step.search.description'); ?></small>
					</span>
				</a>
			</li>
			<li>
				<a class="disabled" data-step="results">
					<div class="stepNumber"> 2 </div>
					<span class="stepDesc"> <?php echo translator::trans('ghafiye.panel.crawler.add.step.results.title'); ?>
						<br>
						<small><?php echo translator::trans('ghafiye.panel.crawler.add.step.results.description'); ?></small>
					</span>
				</a>
			</li>
			<li>
				<a class="disabled" data-step="result-info">
					<div class="stepNumber"> 3 </div>
					<span class="stepDesc"> <?php echo translator::trans('ghafiye.panel.crawler.add.step.result-info.title'); ?>
						<br>
						<small><?php echo translator::trans('ghafiye.panel.crawler.add.step.result-info.description'); ?></small>
					</span>
				</a>
			</li>
			<li>
				<a class="disabled" data-step="import">
					<div class="stepNumber"> 4 </div>
					<span class="stepDesc"> <?php echo translator::trans('ghafiye.panel.crawler.add.step.import.title'); ?>
						<br>
						<small><?php echo translator::trans('ghafiye.panel.crawler.add.step.import.description'); ?></small>
					</span>
				</a>
			</li>
		</ul>
	</div>
</div>
<div class="step search active import">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<div class="panel panel-white panel-search">
				<div class="panel-heading">
					<i class="fa fa-search"></i> <span class="result-total"></span> <?php echo translator::trans('ghafiye.panel.crawler.search'); ?>
					<div class="panel-tools">
						<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
					</div>
				</div>
				<div class="panel-body">
					<form class="crawler-add-form" action="<?php echo userpanel\url('crawler/queue/search'); ?>" method="post">
					<?php
						$feilds = [
							[
								'name' => 'type',
								'type' => 'hidden'
							],
							[
								'name' => 'name',
								'input-group' => $this->getTypeInputGroup()
							]
						];
						foreach($feilds as $input){
							$this->createField($input);
						}
						?>
						<div class="row">
							<div class="col-sm-6 col-sm-offset-3">
								<button class="btn btn-primary btn-block" type="submit"><?php echo translator::trans('ghafiye.panel.crawler.doSearch'); ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 results-search" style="display: none">
			 <h3> <?php echo translator::trans('ghafiye.panel.search.results'); ?> </h3>
		</div>
		<div class="col-xs-12 results">
			
		</div>
	</div>
</div>
<div class="step result-info import">
	<div class="row">
		<div class="col-sm-12">
			<div class="well">
			</div>
		</div>
	</div>
</div>
<?php
	$this->the_footer();
