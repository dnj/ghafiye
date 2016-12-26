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
$person = $this->getperson();
?>
<div class="row">
	<div class="col-md-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <i class="fa fa-edit"></i>
	            <span><?php echo translator::trans("edit").' '.translator::trans("person").' #'.$person->id; ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
	        </div>
	        <div class="panel-body">
	            <div class="table-responsive">
	                <form id="editPerson" class="create_form" action="<?php echo userpanel\url('persons/edit/'.$person->id); ?>" method="post" enctype="multipart/form-data">
	                    <div class="col-md-6">
	                        <?php $this->createField(array(
								'name' => 'name_prefix',
								'label' => translator::trans("ghafiye.panel.person.name_prefix"),
								'value' => $person->title
							));
							?>
	                        <?php $this->createField(array(
								'name' => 'middle_name',
								'label' => translator::trans("ghafiye.panel.person.middle_name"),
								'value' => $person->title
							));
							?>
	                        <?php $this->createField(array(
								'name' => 'name_suffix',
								'label' => translator::trans("ghafiye.panel.person.name_suffix"),
								'value' => $person->title
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
										        <img src="<?php echo $this->getImage($person->avatar); ?>" alt="personImage">
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
										        <img class="cover" src="<?php echo $this->getImage($person->cover); ?>" alt="personImage">
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
							<div class="panel panel-default">
								<!-- start: TAGS PANEL -->
						        <div class="panel-heading">
						            <i class="fa fa-external-link-square"></i> <?php echo translator::trans("ghafiye.panel.person.translated.names"); ?>
						            <div class="panel-tools">
										<?php if($this->canNameAdd){ ?>
										<a class="btn btn-xs btn-link tooltips" title="" href="#addName" data-toggle="modal" data-original-title=""><i class="fa fa-plus"></i></a>
										<?php } ?>
						                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
						            </div>
						        </div>
						        <div class="panel-body">
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
											<?php foreach($person->names as $name){ ?>
									        <tr data-lang="<?php echo $name->lang; ?>">
									            <td class="column-left"><?php
												$this->createField(array(
													'type' => 'hidden',
													'name' => 'names['.$name->lang.']',
													'value' => $name->name
												));
												echo translator::trans("translations.langs.{$name->lang}");
												?></td>
									            <td class="column-right"><a href="#" data-lang="<?php echo $name->lang; ?>" data-type="text" data-pk="1" data-original-title="<?php echo $name->name; ?>" class="editable editable-click name" style="display: inline;"><?php echo $name->name; ?></a></td>
												<?php
												if($hasButtons){
													echo("<td class=\"center\">".$this->genButtons()."</td>");
												}
												?>
									        </tr>
											<?php } ?>
									    </tbody>
									</table>
								</div>
								<!-- end: TAGS PANEL -->
						    </div>
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
<!-- end: BASIC person EDIT -->
<?php
	$this->the_footer();
