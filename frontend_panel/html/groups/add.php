<?php
use \packages\base\json;
use \packages\base\frontend\theme;
use \packages\base\translator;
use \packages\userpanel;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <i class="fa fa-edit"></i>
	            <span><?php echo translator::trans("add").' '.translator::trans("group"); ?></span>
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
							<?php $this->createField(array(
								'name' => 'title',
								'label' => translator::trans("ghafiye.panel.group.title")
							));
							?>
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
<!-- end: BASIC group EDIT -->
<?php
	$this->the_footer();
