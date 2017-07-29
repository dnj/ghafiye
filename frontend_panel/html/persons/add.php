<?php
use \packages\base;
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
	            <span><?php echo translator::trans("ghafiye.panel.persons.add"); ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
	        </div>
	        <div class="panel-body">
	                <form id="addPerson" class="person_add_form" action="<?php echo userpanel\url('persons/add'); ?>" method="post" enctype="multipart/form-data">
	                    <div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label"><?php echo translator::trans("ghafiye.panel.person.avatar"); ?></label>
									<div class="center avatarPreview person-image">
										<input name="avatar" type="file">
										<img src="<?php echo $this->defaultAvatar(); ?>" class="preview img-responsive" alt="چهرک">
										<div class="button-group">
											<button type="button" class="btn btn-teal btn-sm btn-upload"><i class="fa fa-pencil"></i></button>
											<button type="button" class="btn btn-bricky btn-sm btn-remove" data-default="<?php echo $this->defaultAvatar(); ?>"><i class="fa fa-times"></i></button>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-9">
								<div class="form-group">
									<label class="control-label"><?php echo translator::trans("ghafiye.panel.person.cover"); ?></label>
									<div class="center avatarPreview person-cover">
										<input name="cover" type="file">
										<img src="<?php echo $this->defaultAvatar(); ?>" class="preview img-responsive" alt="تصویر زمینه">
										<div class="button-group">
											<button type="button" class="btn btn-teal btn-sm btn-upload"><i class="fa fa-pencil"></i></button>
											<button type="button" class="btn btn-bricky btn-sm btn-remove" data-default="<?php echo $this->defaultAvatar(); ?>"><i class="fa fa-times"></i></button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">

								<?php $this->createField(array(
									'name' => 'musixmatch_id',
									'label' => translator::trans("ghafiye.panel.person.musixmatch_id"),
									'ltr' => true
								));
								$this->createField(array(
									'name' => 'gender',
									'type' => 'select',
									'label' => translator::trans("ghafiye.panel.person.gender"),
									'options' => $this->getGenderForSelect()
								));
								?>
							</div>
							<div class="col-sm-4">
								<div class="row">
									<div class="col-sm-6">
									<?php
									$this->createField(array(
										'name' => 'first_name',
										'label' => translator::trans("ghafiye.panel.person.first_name")
									));
									?>
									</div>
									<div class="col-sm-6">
									<?php
									$this->createField(array(
										'name' => 'last_name',
										'label' => translator::trans("ghafiye.panel.person.last_name")
									));
									?>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
									<?php
									$this->createField(array(
										'name' => 'name_prefix',
										'label' => translator::trans("ghafiye.panel.person.name_prefix")
									));
									?>
									</div>
									<div class="col-sm-6">
									<?php
									$this->createField(array(
										'name' => 'name_suffix',
										'label' => translator::trans("ghafiye.panel.person.name_suffix")
									));
									?>
									</div>
								</div>
								<?php
								$this->createField(array(
									'name' => 'middle_name',
									'label' => translator::trans("ghafiye.panel.person.middle_name")
								));
								?>
							</div>
							<div class="col-sm-5">
								<div class="panel panel-white">
									<div class="panel-heading">
										<i class="fa fa-language"></i> <?php echo translator::trans("ghafiye.panel.person.translated.names"); ?>
										<div class="panel-tools">
											<a class="btn btn-xs btn-link tooltips" title="" href="#addName" data-toggle="modal" data-original-title=""><i class="fa fa-plus"></i></a>
											<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
										</div>
									</div>
									<div class="panel-body">
										<div class="table-responsive">
											<table class="table table-bordered table-striped table-names">
												<?php
												$hasButtons = $this->hasButtons();
												?>
												<thead>
													<th><?php echo translator::trans("ghafiye.panel.person.name.lang"); ?></th>
													<th><?php echo translator::trans("ghafiye.panel.person.translated.name"); ?></th>
													<?php if($hasButtons){ ?><th></th><?php } ?>
												</thead>
												<tbody class="langs" data-langs='<?php echo json\encode($this->getLangsForSelect()); ?>'>
													
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2 col-sm-3 col-xs-5">
								<a href="<?php echo userpanel\url('persons'); ?>" class="btn btn-default btn-block"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans("back"); ?></a>
							</div>
							<div class="col-md-2 col-sm-3 col-md-offset-8 col-sm-offset-6 col-xs-5 col-xs-offset-2">
								<button type="submit" class="btn btn-success btn-block"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("save"); ?></button>
							</div>
						</div>
					</form>
	            </div>
	        </div>
	    </div>
	</div>
</div>

<div class="modal fade" id="addName" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('ghafiye.add_new.name'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="addnameform" class="form-horizontal" action="#" method="post">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'name' => 'lang',
					'type' => 'select',
					'id' => "selectLang",
					'label' => translator::trans("ghafiye.panel.person.name.lang"),
					'options' => $this->getLangsForSelect()
				),
				array(
					'name' => 'name',
					'label' => translator::trans('ghafiye.panel.person.translated.name')
				)
			);
			foreach($feilds as $input){
				echo $this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="addnameform" class="btn btn-success"><?php echo translator::trans("confrim"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cancel'); ?></button>
	</div>
</div>
<?php $this->the_footer(); ?>