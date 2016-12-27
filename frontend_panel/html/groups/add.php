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
	                <form class="create_form" action="<?php echo userpanel\url('groups/add'); ?>" method="post" enctype="multipart/form-data">
						<div class="col-md-5">
							<div class="form-group">
								<label class="control-label"><?php echo translator::trans("ghafiye.panel.group.avatar"); ?></label>
								<div class="center group-image-box">
									<div class="fileupload fileupload-new" data-provides="fileupload">
										<div class="group-image">
											<div class="fileupload-new thumbnail">
										        <img src="<?php echo $this->getImage(); ?>" alt="groupImage">
										    </div>
											<div class="fileupload-preview fileupload-exists thumbnail"></div>
										    <div class="group-image-buttons">
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
						<div class="col-md-7">
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
						<div class="col-md-12">
			                <p>
			                    <a href="<?php echo userpanel\url('groups'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('return'); ?></a>
			                    <button type="submit" class="btn btn-yellow"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("confrim") ?></button>
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
