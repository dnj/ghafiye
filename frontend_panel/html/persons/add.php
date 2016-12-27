<?php
use \packages\base;
use \packages\base\json;
use \packages\base\http;
use \packages\base\translator;
use \packages\base\db\dbObject;
use \packages\base\views\FormError;

use \packages\userpanel;
use \packages\userpanel\date;

use \themes\clipone\utility;

use \packages\gamakey\plan;

$this->the_header();
?>
<div class="row">
    <div class="col-md-12">
        <!-- start: BASIC person EDIT -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-plus"></i>
                <span><?php echo translator::trans("add"); ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <form id="editPerson" class="create_form" action="<?php echo userpanel\url('persons/add') ?>" method="post" enctype="multipart/form-data">
						<div class="col-md-6">
	                        <?php $this->createField(array(
								'name' => 'name_prefix',
								'label' => translator::trans("ghafiye.panel.person.name_prefix"),
							));
							?>
	                        <?php $this->createField(array(
								'name' => 'middle_name',
								'label' => translator::trans("ghafiye.panel.person.middle_name"),
							));
							?>
	                        <?php $this->createField(array(
								'name' => 'name_suffix',
								'label' => translator::trans("ghafiye.panel.person.name_suffix"),
							));
							?>
                        </div>
						<div class="col-md-6">
							<?php
							$this->createField(array(
								'name' => 'first_name',
								'label' => translator::trans("ghafiye.panel.person.first_name")
							));
							?>
							<?php
							$this->createField(array(
								'name' => 'last_name',
								'label' => translator::trans("ghafiye.panel.person.last_name")
							));
							?>
							<?php
							$this->createField(array(
								'name' => 'gender',
								'type' => 'select',
								'label' => translator::trans("ghafiye.panel.person.gender"),
								'options' => $this->getGenderForSelect()
							));
							?>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label"><?php echo translator::trans("ghafiye.panel.person.avatar"); ?></label>
								<div class="center person-image-box">
									<div class="fileupload fileupload-new" data-provides="fileupload">
										<div class="person-image">
											<div class="fileupload-new thumbnail">
												<img src="<?php echo $this->getImage(); ?>" alt="personImage">
											</div>
											<div class="fileupload-preview fileupload-exists thumbnail"></div>
											<div class="person-image-buttons">
												<span class="btn btn-teal btn-file btn-sm">
													<span class="fileupload-new">
														<i class="fa fa-pencil"></i>
													</span>
													<span class="fileupload-exists">
														<i class="fa fa-pencil"></i>
													</span>
													<input name="avatar" type="file">
												</span>
												<a href="#" class="btn fileupload-exists btn-bricky btn-sm" data-dismiss="fileupload">
													<i class="fa fa-times"></i>
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-9">
							<div class="form-group">
								<label class="control-label"><?php echo translator::trans("ghafiye.panel.person.cover"); ?></label>
								<div class="center person-image-box">
									<div class="fileupload fileupload-new" data-provides="fileupload">
										<div class="person-image cover">
											<div class="fileupload-new thumbnail">
												<img class="cover" src="<?php echo $this->getImage(); ?>" alt="personImage">
											</div>
											<div class="fileupload-preview fileupload-exists thumbnail"></div>
											<div class="person-image-buttons">
												<span class="btn btn-teal btn-file btn-sm">
													<span class="fileupload-new">
														<i class="fa fa-pencil"></i>
													</span>
													<span class="fileupload-exists">
														<i class="fa fa-pencil"></i>
													</span>
													<input name="cover" type="file">
												</span>
												<a href="#" class="btn fileupload-exists btn-bricky btn-sm" data-dismiss="fileupload">
													<i class="fa fa-times"></i>
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-6">
								<?php $this->createField(array(
									'name' => 'name',
									'label' => translator::trans("ghafiye.panel.person.translated.name"),
								));
								?>
							</div>
							<div class="col-md-4">
								<?php $this->createField(array(
									'name' => 'lang',
									'type' => 'select',
									'label' => translator::trans("ghafiye.panel.person.name.lang"),
									'options' => $this->getLangsForSelect()
								));
								?>
							</div>
							<div class="col-md-2"></div>
						</div>
						<div class="col-md-12">
			                <p>
			                    <a href="<?php echo userpanel\url('persons'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('return'); ?></a>
			                    <button form="editPerson" type="submit" class="btn btn-yellow"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("update") ?></button>
			                </p>
						</div>
	                </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end: BASIC person EDIT -->
<?php
	$this->the_footer();
