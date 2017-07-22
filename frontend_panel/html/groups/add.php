<?php
use \packages\base\json;
use \packages\base\frontend\theme;
use \packages\base\translator;
use \packages\userpanel;

$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <i class="fa fa-plus"></i>
	            <span><?php echo translator::trans("ghafiye.panle.groups.add"); ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
	        </div>
	        <div class="panel-body">
	            <div class="table-responsive">
	                <form class="group_add_form" action="<?php echo userpanel\url('groups/add'); ?>" method="post" enctype="multipart/form-data">
						<div class="col-sm-3">
							<label class="control-label"><?php echo translator::trans("ghafiye.panel.group.avatar"); ?></label>
							<div class="fileupload fileupload-new" data-provides="fileupload">
								<div class="form-group">
									<div class="user-image avatarPreview">
										<img src="<?php echo $this->getImage(); ?>" class="preview img-responsive">
										<input name="avatar" type="file">
										<div class="button-group">
											<button type="button" class="btn btn-teal btn-sm btn-upload"><i class="fa fa-pencil"></i></button>
											<button type="button" class="btn btn-bricky btn-sm btn-remove" data-default="<?php echo theme::url('assets/images/defaultavatar.jpg'); ?>"><i class="fa fa-times"></i></button>
										</div>
									</div>
								</div>
							</div>
	                    </div>
						<div class="col-sm-9">
							<?php $this->createField(array(
								'name' => 'group-lang',
								'type' => 'select',
								'label' => translator::trans("ghafiye.panel.group.lang"),
								'options' => $this->getLangsForSelect()
							));
							?>
						</div>
						<div class="col-sm-12">
							<div class="panel panel-white">
								<div class="panel-heading">
									<i class="fa fa-pencil"></i> <?php echo translator::trans("ghafiye.panel.group.translated.titles"); ?>
									<div class="panel-tools">
										<a class="btn btn-xs btn-link tooltips" title="" href="#addTitle" data-toggle="modal" data-original-title=""><i class="fa fa-plus"></i></a>
										<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
									</div>
								</div>
								<div class="panel-body">
								<div class="table-responsive">
										<table class="table table-bordered table-striped table-names">
											<thead>
												<th><?php echo translator::trans("ghafiye.panel.group.title.lang"); ?></th>
												<th><?php echo translator::trans("ghafiye.panel.group.translated.title"); ?></th>
												<th></th>
											</thead>
											<tbody class="titles" data-langs='<?php echo json\encode($this->getLangsForSelect()); ?>'>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="panel panel-white">
								<div class="panel-heading">
									<i class="fa fa-users"></i> <?php echo translator::trans("ghafiye.panel.group.persons"); ?>
									<div class="panel-tools">
										<a class="btn btn-xs btn-link tooltips" title="" href="#addPerson" data-toggle="modal" data-original-title=""><i class="fa fa-plus"></i></a>
										<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
									</div>
								</div>
								<div class="panel-body">
									<table class="table table-bordered table-striped table-names">
										<thead>
											<th><?php echo translator::trans("ghafiye.panel.group.person.name"); ?></th>
											<th></th>
										</thead>
										<tbody class="persons">
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<p>
								<a href="<?php echo userpanel\url('groups'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('return'); ?></a>
								<button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("confrim") ?></button>
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
					'label' => translator::trans("ghafiye.panel.group.title.lang"),
					'options' => $this->getLangsForSelect()
				),
				array(
					'name' => 'title',
					'label' => translator::trans('ghafiye.panel.group.translated.title')
				)
			);
			foreach($feilds as $input){
				echo $this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="addTitleform" class="btn btn-success"><?php echo translator::trans("confrim"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cancel'); ?></button>
	</div>
</div>
<div class="modal fade" id="addPerson" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('ghafiye.panel.group.add.person'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="addPersonForm" class="form-horizontal" action="#" method="post">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'name' => 'person_name',
					'label' => translator::trans("ghafiye.panel.group.person.name")
				),
				array(
					'name' => 'person',
					'type' => 'hidden'
				)
			);
			foreach($feilds as $input){
				echo $this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="addPersonForm" class="btn btn-success"><?php echo translator::trans("confrim"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cancel'); ?></button>
	</div>
</div>
<?php
	$this->the_footer();
