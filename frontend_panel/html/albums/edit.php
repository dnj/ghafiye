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
$album = $this->getAlbum();
?>
<div class="row">
	<div class="col-md-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <i class="fa fa-edit"></i>
	            <span><?php echo translator::trans("edit").' '.translator::trans("album").' #'.$album->id; ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
	        </div>
	        <div class="panel-body">
	            <div class="table-responsive">
	                <form class="album_edit_form" action="<?php echo userpanel\url('albums/edit/'.$album->id); ?>" method="post" enctype="multipart/form-data">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label"><?php echo translator::trans("ghafiye.panel.album.avatar"); ?></label>
								<div class="center album-image-box">
									<div class="fileupload fileupload-new" data-provides="fileupload">
										<div class="album-image">
											<div class="fileupload-new thumbnail">
										        <img src="<?php echo $this->getImage($album->image); ?>" alt="albumImage">
										    </div>
											<div class="fileupload-preview fileupload-exists thumbnail"></div>
										    <div class="album-image-buttons">
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
							<?php $this->createField(array(
								'name' => 'musixmatch_id',
								'label' => translator::trans("ghafiye.panel.album.musixmatch_id"),
								'ltr' => true
							));
							?>
							<?php $this->createField(array(
								'name' => 'album-lang',
								'type' => 'select',
								'label' => translator::trans("ghafiye.panel.album.lang"),
								'options' => $this->getLangsForSelect()
							));
							?>
						</div>
						<div class="col-md-12">
							<div class="panel panel-default">
								<!-- start: TAGS PANEL -->
						        <div class="panel-heading">
						            <i class="fa fa-pencil"></i> <?php echo translator::trans("ghafiye.panel.album.translated.titles"); ?>
						            <div class="panel-tools">
										<a class="btn btn-xs btn-link tooltips" title="" href="#addTitle" data-toggle="modal" data-original-title=""><i class="fa fa-plus"></i></a>
						                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
						            </div>
						        </div>
						        <div class="panel-body">
									<table class="table table-bordered table-striped table-names">
										<thead>
											<th><?php echo translator::trans("ghafiye.panel.album.title.lang"); ?></th>
											<th><?php echo translator::trans("ghafiye.panel.album.translated.title"); ?></th>
											<th></th>
										</thead>
									    <tbody class="titles" data-langs='<?php echo json\encode($this->getLangsForSelect()); ?>'>
											<?php foreach($album->titles as $title){ ?>
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
								<!-- end: TAGS PANEL -->
						    </div>
						</div>
						<div class="col-md-12">
							<div class="panel panel-default">
								<!-- start: TAGS PANEL -->
						        <div class="panel-heading">
						            <i class="fa fa-music"></i> <?php echo translator::trans("ghafiye.panel.album.songs"); ?>
						            <div class="panel-tools">
										<a class="btn btn-xs btn-link tooltips" title="" href="#addSong" data-toggle="modal" data-original-title=""><i class="fa fa-plus"></i></a>
						                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
						            </div>
						        </div>
						        <div class="panel-body">
									<table class="table table-bordered table-striped table-names">
										<thead>
											<th><?php echo translator::trans("ghafiye.panel.album.song.name"); ?></th>
											<th></th>
										</thead>
									    <tbody class="songs">
											<?php
											foreach($album->songs as $song){
											?>
									        <tr data-song="<?php echo $song->id; ?>">
									            <td class="column-left">
												<?php
													$this->createField(array(
														'type' => 'hidden',
														'name' => 'songs[]',
														'value' => $song->id
													));
												?>
												<a href="<?php echo userpanel\url("songs/edit/{$song->id}"); ?>"><?php echo($song->title($song->lang)); ?></a>
												</td>
												<td class="center"><a href="#" class="btn btn-xs btn-bricky tooltips song-del" title="" data-original-title="<?php echo translator::trans("delete"); ?>"><i class="fa fa-times"></i></a></td>
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
			                    <a href="<?php echo userpanel\url('albums'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('return'); ?></a>
			                    <button type="submit" class="btn btn-yellow"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("update") ?></button>
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
					'label' => translator::trans("ghafiye.panel.album.title.lang"),
					'options' => $this->getLangsForSelect()
				),
				array(
					'name' => 'title',
					'label' => translator::trans('ghafiye.panel.album.translated.title')
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
<div class="modal fade" id="addSong" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('ghafiye.panel.album.add.song'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="addSongForm" class="form-horizontal" action="#" method="post">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'name' => 'song_name',
					'label' => translator::trans("ghafiye.panel.album.song.name")
				),
				array(
					'name' => 'song',
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
		<button type="submit" form="addSongForm" class="btn btn-success"><?php echo translator::trans("confrim"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cancel'); ?></button>
	</div>
</div>
<!-- end: BASIC album EDIT -->
<?php
	$this->the_footer();
