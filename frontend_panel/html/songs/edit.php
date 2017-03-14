<?php
use \packages\base\json;
use \packages\base\translator;

use \packages\userpanel;
use \packages\ghafiye\person;
use \packages\ghafiye\song\lyric;

$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <i class="fa fa-edit"></i>
	            <span><?php echo translator::trans("ghafiye.editSong"); ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
	        </div>
	        <div class="panel-body">
				<form id="editsong" class="create_form" action="<?php echo userpanel\url('songs/edit/'.$this->song->id, array('langLyric' => $this->getDataForm('lyric_lang'))); ?>" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col-xs-3">
							<div class="form-group">
								<label class="control-label"><?php echo translator::trans("ghafiye.panel.song.image"); ?></label>
								<div class="center song-image-box">
									<div class="fileupload fileupload-new" data-provides="fileupload">
										<div class="song-image">
											<div class="fileupload-new thumbnail">
												<img src="<?php echo $this->getSongImage(); ?>" alt="songImage">
											</div>
											<div class="fileupload-preview fileupload-exists thumbnail"></div>
											<div class="song-image-buttons">
												<span class="btn btn-teal btn-file btn-sm">
													<span class="fileupload-new">
														<i class="fa fa-pencil"></i>
													</span>
													<span class="fileupload-exists">
														<i class="fa fa-pencil"></i>
													</span>
													<input name="image" type="file">
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
						<div class="col-xs-9">
							<div class="col-xs-6">
								<?php 
								$this->createField(array(
									'name' => 'musixmatch_id',
									'label' => translator::trans("ghafiye.panel.song.musixmatch_id"),
									'ltr' => true
								));
								$this->createField(array(
									'name' => 'spotify_id',
									'label' => translator::trans("ghafiye.panel.song.spotify_id"),
									'ltr' => true
								));
								$this->createField(array(
									'name' => 'album',
									'type' => 'hidden'
								));
								$this->createField(array(
									'name' => 'album_name',
									'label' => translator::trans("ghafiye.panel.song.album")
								));
								$this->createField(array(
									'name' => 'group_name',
									'label' => translator::trans("ghafiye.panel.song.group")
								));
								$this->createField(array(
									'name' => 'group',
									'type' => 'hidden'
								));
								?>
							</div>
							<div class="col-xs-6">
								<?php
								$this->createField(array(
									'name' => 'duration',
									'label' => translator::trans("ghafiye.panel.song.duration"),
									'ltr' => true,
									'type' => 'number'
								));
								$this->createField(array(
									'name' => 'genre',
									'type' => 'select',
									'label' => translator::trans("ghafiye.panel.song.genre"),
									'options' => $this->getGenreForSelect()
								));
								$this->createField(array(
									'name' => 'lang',
									'type' => 'select',
									'label' => translator::trans("ghafiye.panel.song.lang"),
									'options' => $this->getLangForSelect()
								));
								$this->createField(array(
									'name' => 'status',
									'type' => 'select',
									'label' => translator::trans("ghafiye.panel.song.status"),
									'options' => $this->getStatusForSelect()
								));
								?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<div class="panel panel-default">
						        <div class="panel-heading">
						            <i class="fa fa-users"></i> <?php echo translator::trans("ghafiye.panel.song.persons"); ?>
						            <div class="panel-tools">
										<a class="btn btn-xs btn-link tooltips" title="" href="#addPerson" data-toggle="modal" data-original-title=""><i class="fa fa-plus"></i></a>
						                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
						            </div>
						        </div>
						        <div class="panel-body">
									<table class="table table-bordered table-striped table-names">
										<thead>
											<th><?php echo translator::trans("ghafiye.panel.song.person"); ?></th>
											<th><?php echo translator::trans("ghafiye.panel.song.person.role"); ?></th>
											<th><?php echo translator::trans("ghafiye.panel.song.person.primary"); ?></th>
											<th></th>
										</thead>
									    <tbody class="persons">
											<?php
											$persons = $this->getDataForm("persons");
											foreach($persons as $person){
												$person = person::byId($person['id']);
											?>
									        <tr data-person="<?php echo $person->id; ?>">
									            <td class="column-left">
												<?php
													$this->createField(array(
														'type' => 'hidden',
														'name' => 'persons['.$person->id.'][id]'
													));
												?>
												<a href="<?php echo userpanel\url("persons/edit/{$person->id}"); ?>" target="_blank"><?php echo($person->name()); ?></a>
												</td>
												<td>
												<?php
												$this->createField(array(
													'type' => 'select',
													'name' => 'persons['.$person->id.'][role]',
													'class' => 'form-control person-role',
													'options' => $this->getRolesForSelect()
												));
												?>
												</td>
												<td class="center">
												<?php
												$this->createField(array(
													'type' => 'checkbox',
													'name' => 'persons['.$person->id.'][primary]',
													'options' => array(
														array(
															'value' => 1,
															'class' => "grey person-primary"
														)
													)
												));
												?>
												</td>
												<td class="center"><a href="#" class="btn btn-xs btn-bricky tooltips person-del" title="" data-original-title="<?php echo translator::trans("delete"); ?>"><i class="fa fa-times"></i></a></td>
									        </tr>
											<?php } ?>
									    </tbody>
									</table>
								</div>
						    </div>
						</div>
						<div class="col-xs-6">
							<div class="panel panel-default">
								<div class="panel-heading">
									<i class="fa fa-external-link-square"></i> <?php echo translator::trans("ghafiye.panel.song.translated.titles"); ?>
									<div class="panel-tools">
										<a class="btn btn-xs btn-link tooltips" title="" href="#addtitle" data-toggle="modal" data-original-title=""><i class="fa fa-plus"></i></a>
										<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
									</div>
								</div>
								<div class="panel-body">
									<table class="table table-bordered table-striped table-titles">
										<thead>
											<th><?php echo translator::trans("ghafiye.panel.song.lang"); ?></th>
											<th><?php echo translator::trans("ghafiye.panel.song.title"); ?></th>
											<th></th>
										</thead>
										<tbody class="langs" data-langs='<?php echo json\encode($this->getLangForSelect()); ?>'>
											<?php foreach($this->song->titles as $title){ ?>
											<tr data-lang="<?php echo $title->lang; ?>">
												<td class="column-left">
												<?php
												$this->createField(array(
													'type' => 'hidden',
													'name' => 'titles['.$title->lang.']'
												));
												echo translator::trans("translations.langs.{$title->lang}");
												?></td>
												<td class="column-right"><a href="#" data-lang="<?php echo $title->lang; ?>" data-type="text" data-pk="1" data-original-title="<?php echo $title->title; ?>" class="editable editable-click title" style="display: inline;"><?php echo $title->title; ?></a></td>
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
						<div class="col-xs-12">
							<div class="panel panel-default">
								<div class="panel-heading">
									<i class="fa fa-music"></i>
									<span><?php echo translator::trans("ghafiye.editSong.lyric"); ?></span>
									<div class="panel-tools">
										<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans("ghafiye.song.changeLang"); ?>" href="#changeLyricLang" data-toggle="modal" data-original-title=""><i class="fa fa-plus"></i></a>
										<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
									</div>
								</div>
								<div class="panel-body">
									<div class="lyricFields">
										<?php 
										$this->createField(array(
											'type' => 'hidden',
											'name' => 'lyric_lang',
											'class' => 'lyrics'
										));
										$count = count($this->getDataForm('lyric'));
										$ltr = lyric::is_ltr($this->song->lang);
										$lang = $this->song->lang;
										for($i=0;$i < $count; $i++){
										?>
										<div class="row lyrics" data-lyriclang="<?php echo $lang; ?>">
											<div class="col-xs-3">
												<?php
												$this->createField(array(
													'type' => 'hidden',
													'name' => 'lyric['.$i.'][id]',
													'class' => 'form-control lyric_id'
												));
												$this->createField(array(
													'name' => 'lyric['.$i.'][time]',
													'class' => 'form-control lyric_time',
													'ltr' => true
												)); ?>
											</div>
											<div class="col-xs-8">
												<?php
												$this->createField(array(
													'name' => 'lyric['.$i.'][text]',
													'class' => 'form-control lyric_text',
													'ltr' => $ltr
												));
												?>
											</div>
										</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<p>
							<a href="<?php echo userpanel\url('songs'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('return'); ?></a>
							<button type="submit" class="btn btn-yellow"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("update") ?></button>
						</p>
					</div>
				</form>
	        </div>
	    </div>
	</div>
</div>
<div class="modal fade" id="addtitle" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('ghafiye.song.addTitle'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="addtitleform" class="form-horizontal" action="#" method="post">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'name' => 'lang',
					'type' => 'select',
					'id' => "selectLang",
					'label' => translator::trans("ghafiye.panel.song.lang"),
					'options' => $this->getLangForSelect()
				),
				array(
					'name' => 'title',
					'label' => translator::trans('ghafiye.panel.song.title')
				)
			);
			foreach($feilds as $input){
				echo $this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="addtitleform" class="btn btn-success"><?php echo translator::trans("confrim"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cancel'); ?></button>
	</div>
</div>
<div class="modal fade" id="changeLyricLang" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('ghafiye.song.translate.otherLang'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="changeLyricForm" class="form-horizontal" action="#" method="post" data-song="<?php echo($this->song->id); ?>">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'name' => 'lang',
					'type' => 'select',
					'label' => translator::trans("ghafiye.panel.song.lang"),
					'options' => $this->getLangForSelect()
				)
			);
			foreach($feilds as $input){
				echo $this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="changeLyricForm" class="btn btn-success"><?php echo translator::trans("confrim"); ?></button>
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
					'label' => translator::trans("ghafiye.panel.song.person")
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
