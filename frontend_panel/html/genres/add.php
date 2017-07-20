<?php
use \packages\base\json;
use \packages\base\translator;
use \packages\userpanel;

$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <i class="fa fa-plus"></i>
	            <span><?php echo translator::trans("add").' '.translator::trans("genre"); ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
	        </div>
	        <div class="panel-body">
	            <div class="table-responsive">
	                <form id="addgenre" class="genre_add_form" action="<?php echo userpanel\url('genres/add'); ?>" method="post" enctype="multipart/form-data">
	                    <div class="col-sm-6">
	                        <?php $this->createField(array(
								'name' => 'musixmatch_id',
								'label' => translator::trans("ghafiye.panel.genre.musixmatch_id"),
								'ltr' => true
							));
							?>
						</div>
						<div class="col-sm-12">
							<div class="panel panel-white">
						        <div class="panel-heading">
						            <i class="fa fa-pencil"></i> <?php echo translator::trans("ghafiye.panel.genre.translated.titles"); ?>
						            <div class="panel-tools">
										<a class="btn btn-xs btn-link tooltips" title="" href="#addTitle" data-toggle="modal" data-original-title=""><i class="fa fa-plus"></i></a>
						                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
						            </div>
						        </div>
						        <div class="panel-body">
									<div class="table-responsive">
										<table class="table table-bordered table-striped table-titles">
											<thead>
												<th><?php echo translator::trans("ghafiye.panel.genre.translated.lang"); ?></th>
												<th><?php echo translator::trans("ghafiye.panel.genre.translated.title"); ?></th>
												<th></th>
											</thead>
											<tbody class="langs" data-langs='<?php echo json\encode($this->getLangsForSelect()); ?>'>
											</tbody>
										</table>
									</div>
								</div>
						    </div>
						</div>
						<div class="col-sm-12">
			                <p>
			                    <a href="<?php echo userpanel\url('genres'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('ghafiye.return'); ?></a>
			                    <button form="addgenre" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("confrim") ?></button>
			                </p>
						</div>
					</form>
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div class="modal fade" id="addTitle" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('ghafiye.add_new.name'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="addTitleform" class="form-horizontal" action="#" method="post">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'name' => 'lang',
					'type' => 'select',
					'id' => "selectLang",
					'label' => translator::trans("ghafiye.panel.genre.translated.lang"),
					'options' => $this->getLangsForSelect()
				),
				array(
					'name' => 'title',
					'label' => translator::trans('ghafiye.panel.genre.translated.title')
				)
			);
			foreach($feilds as $input){
				$this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="addTitleform" class="btn btn-success"><?php echo translator::trans("confrim"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cancel'); ?></button>
	</div>
</div>
<?php
	$this->the_footer();
