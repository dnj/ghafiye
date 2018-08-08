<?php
use \packages\base\json;
use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\userpanel;
$this->the_header();
$group = $this->getGroup();
?>
<div class="row">
	<div class="col-xs-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <i class="fa fa-edit"></i>
	            <span><?php echo translator::trans("ghafiye.panel.groups.edit").' #'.$group->id; ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
	        </div>
	        <div class="panel-body">
				<form class="group_edit_form" action="<?php echo userpanel\url('groups/edit/'.$group->id); ?>" method="post" enctype="multipart/form-data">
					<div class="row">
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
							<div class="form-group">
								<label class="control-label"><?php echo translator::trans("ghafiye.panel.group.cover"); ?></label>
								<div class="center avatarPreview group-cover">
									<input name="cover" type="file">
									<img src="<?php echo $this->defaultCover(); ?>" class="preview img-responsive" alt="تصویر زمینه">
									<div class="button-group">
										<button type="button" class="btn btn-teal btn-sm btn-upload"><i class="fa fa-pencil"></i></button>
										<button type="button" class="btn btn-bricky btn-sm btn-remove" data-default="<?php echo $this->defaultCover(); ?>"><i class="fa fa-times"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<?php $this->createField(array(
								'name' => 'group-lang',
								'type' => 'select',
								'label' => translator::trans("ghafiye.panel.group.lang"),
								'options' => $this->getLangsForSelect()
							));
							?>
						</div>
						<div class="col-sm-6">
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
												<?php foreach($group->getTitles() as $title){ ?>
												<tr data-lang="<?php echo $title->lang; ?>">
													<td class="column-left"><?php
													$this->createField(array(
														'type' => 'hidden',
														'name' => 'titles['.$title->lang.']',
														'value' => $title->title
													));
													echo translator::trans("translations.langs.{$title->lang}");
													?></td>
													<td class="column-right"><a href="#" data-lang="<?php echo $title->lang; ?>" data-type="text" data-pk="1" data-original-title="<?php echo $title->title; ?>" class="editable editable-click title" style="display: inline;"><?php echo $title->title; ?></a></td>
													<td class="center"><a href="#" class="btn btn-xs btn-bricky tooltips title-del" title="" data-original-title="<?php echo translator::trans("delete"); ?>"><i class="fa fa-times"></i></a></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
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
											<?php
											foreach($group->persons as $person){
												$person = $person->person;
											?>
											<tr data-person="<?php echo $person->id; ?>">
												<td class="column-left">
												<?php
													$this->createField(array(
														'type' => 'hidden',
														'name' => 'persons[]',
														'value' => $person->id
													));
												?>
												<a href="<?php echo userpanel\url("persons/edit/{$person->id}"); ?>"><?php echo($person->name()); ?></a>
												</td>
												<td class="center"><a href="#" class="btn btn-xs btn-bricky tooltips person-del" title="" data-original-title="<?php echo translator::trans("delete"); ?>"><i class="fa fa-times"></i></a></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<p>
								<a href="<?php echo userpanel\url('groups'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('ghafiye.return'); ?></a>
								<button type="submit" class="btn btn-teal"><i class="fa fa-edit"></i> <?php echo translator::trans("ghafiye.update") ?></button>
							</p>
						</div>
					</div>
				</form>
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
