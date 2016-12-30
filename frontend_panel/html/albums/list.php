<?php
use \packages\base;
use \packages\base\translator;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\date;

use \themes\clipone\utility;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<!-- start: BASIC TABLE PANEL -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="clip-globe"></i> <?php echo translator::trans("ghafiye.panle.albums.list"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('search'); ?>" href="#search" data-toggle="modal" data-original-title=""><i class="fa fa-search"></i></a>
					<?php if($this->canAdd){ ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('add'); ?>" href="<?php echo userpanel\url('albums/add'); ?>"><i class="fa fa-plus"></i></a>
					<?php } ?>
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<?php
						$hasButtons = $this->hasButtons();
						?>
						<thead>
							<tr>
								<th class="center">#</th>
								<th><?php echo translator::trans('ghafiye.panel.album.lang'); ?></th>
								<th><?php echo translator::trans('ghafiye.panel.album.title'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->getalbumsLists() as $album){
								$this->setButtonParam('edit', 'link', userpanel\url("albums/edit/".$album->id));
								$this->setButtonParam('delete', 'link', userpanel\url("albums/delete/".$album->id));
							?>
							<tr>
								<td><?php echo $album->id; ?></td>
								<td><?php echo translator::trans("translations.langs.{$album->lang}"); ?></td>
								<td><?php echo $album->getTitle(); ?></td>
								<?php
								if($hasButtons){
									echo("<td class=\"center\">".$this->genButtons()."</td>");
								}
								?>
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<?php $this->paginator(); ?>
			</div>
		</div>
		<!-- end: BASIC TABLE PANEL -->
	</div>
</div>
<div class="modal fade" id="search" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('search'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="AlbumSearch" class="form-horizontal" action="<?php echo userpanel\url("albums"); ?>" method="GET">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'name' => 'song',
					'type' => 'hidden'
				),
				array(
					'name' => 'id',
					'type' => 'number',
					'label' => translator::trans("ghafiye.panel.album.id"),
					'ltr' => true
				),
				array(
					'name' => 'word',
					'label' => translator::trans("ghafiye.panel.album.word.key")
				),
				array(
					'name' => 'song_name',
					'label' => translator::trans("ghafiye.panel.album.song.name")
				),
				array(
					'type' => 'select',
					'label' => translator::trans('ghafiye.panel.album.lang'),
					'name' => 'lang',
					'options' => $this->getLnagsForSelect()
				),
				array(
					'type' => 'select',
					'label' => translator::trans('search.comparison'),
					'name' => 'comparison',
					'options' => $this->getComparisonsForSelect()
				)
			);
			foreach($feilds as $input){
				echo $this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="AlbumSearch" class="btn btn-success"><?php echo translator::trans("search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cancel'); ?></button>
	</div>
</div>
<?php
$this->the_footer();
