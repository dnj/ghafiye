<?php
use \packages\base;
use \packages\base\translator;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\date;

use \packages\ghafiye\song;

use \themes\clipone\utility;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="clip-globe"></i> <?php echo translator::trans("ghafiye.panel.songs.list"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('search'); ?>" href="#search" data-toggle="modal" data-original-title=""><i class="fa fa-search"></i></a>
					<?php if($this->canAdd){ ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('add'); ?>" href="<?php echo userpanel\url('songs/add'); ?>"><i class="fa fa-plus"></i></a>
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
								<th><?php echo translator::trans('ghafiye.panel.song.lang'); ?></th>
								<th><?php echo translator::trans('ghafiye.panel.song.title'); ?></th>
								<th><?php echo translator::trans('ghafiye.panel.song.album'); ?></th>
								<th><?php echo translator::trans('ghafiye.panel.song.group'); ?></th>
								<th><?php echo translator::trans('ghafiye.panel.song.status'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->getsongsLists() as $song){
								$this->setButtonParam('edit', 'link', userpanel\url("songs/edit/".$song->id));
								$this->setButtonParam('delete', 'link', userpanel\url("songs/delete/".$song->id));
								$statusClass = utility::switchcase($song->status, array(
									'label label-success' => song::publish,
									'label label-warning' => song::draft,
									'label label-danger' => song::Block
								));
								$statusTxt = utility::switchcase($song->status, array(
									'ghafiye.panel.song.status.publish' => song::publish,
									'ghafiye.panel.song.status.draft' => song::draft,
									'ghafiye.panel.song.status.block' => song::Block
								));
							?>
							<tr>
								<td><?php echo $song->id; ?></td>
								<td><?php echo translator::trans("translations.langs.{$song->lang}"); ?></td>
								<td><?php echo $song->title($song->lang); ?></td>
								<td><?php echo ($song->album ? "<a href=\"".userpanel\url("albums/edit/{$song->album->id}")."\">{$song->album->getTitle()}</a>" : "-"); ?></td>
								<td><?php echo ($song->group ? "<a href=\"".userpanel\url("groups/edit/{$song->group->id}")."\">{$song->group->getTitle()}</a>" : "-"); ?></td>
								<td class="hidden-xs"><span class="<?php echo $statusClass; ?>"><?php echo translator::trans($statusTxt); ?></span></td>
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
	</div>
</div>
<div class="modal fade" id="search" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('search'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="songsLists" class="form-horizontal" action="<?php echo userpanel\url("songs"); ?>" method="GET" autocomplete="off">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'name' => 'album',
					'type' => 'hidden'
				),
				array(
					'name' => 'group',
					'type' => 'hidden'
				),
				array(
					'name' => 'person',
					'type' => 'hidden'
				),
				array(
					'name' => 'id',
					'type' => 'number',
					'label' => translator::trans("ghafiye.panel.song.id"),
					'ltr' => true
				),
				array(
					'name' => 'album_name',
					'label' => translator::trans("ghafiye.panel.song.album")
				),
				array(
					'name' => 'group_name',
					'label' => translator::trans("ghafiye.panel.song.group")
				),
				array(
					'name' => 'person_name',
					'label' => translator::trans("ghafiye.panel.song.person")
				),
				array(
					'name' => 'word',
					'label' => translator::trans("ghafiye.panel.song.word.key"),
					'ltr' => true
				),
				array(
					'type' => 'select',
					'label' => translator::trans('ghafiye.panel.song.lang'),
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
		<button type="submit" form="songsLists" class="btn btn-success"><?php echo translator::trans("search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cancel'); ?></button>
	</div>
</div>
<?php
$this->the_footer();
