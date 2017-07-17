<?php
use \packages\base\json;
use \packages\base\translator;
use \packages\userpanel;
$this->the_header();
$genre = $this->getgenre();
?>
<div class="row">
	<div class="col-xs-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <i class="fa fa-edit"></i>
	            <span><?php echo translator::trans("edit").' '.translator::trans("genre").' #'.$genre->id; ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
	        </div>
	        <div class="panel-body">
	            <div class="table-responsive">
	                <form id="editgenre" class="genre_edit_form" action="<?php echo userpanel\url('genres/edit/'.$genre->id); ?>" method="post" enctype="multipart/form-data">
	                    <div class="col-sm-6">
	                        <?php $this->createField(array(
								'name' => 'musixmatch_id',
								'label' => translator::trans("ghafiye.panel.genre.musixmatch_id"),
								'ltr' => true
							));
							?>
						</div>
						<div class="col-sm-12">
							<div class="panel panel-default">
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
												<?php foreach($genre->titles as $title){ ?>
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
						<div class="col-sm-12">
			                <p>
			                    <a href="<?php echo userpanel\url('genres'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('return'); ?></a>
			                    <button form="editgenre" type="submit" class="btn btn-teal"><i class="fa fa-edit"></i> <?php echo translator::trans("ghafiye.update") ?></button>
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
<?php
	$this->the_footer();
