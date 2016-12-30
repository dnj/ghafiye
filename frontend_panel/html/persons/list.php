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
				<i class="clip-users"></i> <?php echo(translator::trans("ghafiye.panle.persons.list")); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('search'); ?>" href="#search" data-toggle="modal" data-original-title=""><i class="fa fa-search"></i></a>
					<?php if($this->canAdd){ ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('add'); ?>" href="<?php echo userpanel\url('persons/add'); ?>"><i class="fa fa-plus"></i></a>
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
								<th><?php echo translator::trans('ghafiye.panel.person.name'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->getPersonsLists() as $person){
								$this->setButtonParam('edit', 'link', userpanel\url("persons/edit/".$person->id));
								$this->setButtonParam('delete', 'link', userpanel\url("persons/delete/".$person->id));
							?>
							<tr>
								<td class="center"><?php echo $person->id; ?></td>
								<td><?php echo $person->name(); ?></td>
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
		<form id="personSearch" class="form-horizontal" action="<?php echo userpanel\url("persons"); ?>" method="GET" autocomplete="off">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'name' => 'id',
					'type' => 'number',
					'label' => translator::trans("ghafiye.panel.person.id"),
					'ltr' => true
				),
				array(
					'name' => 'musixmatch_id',
					'label' => translator::trans("ghafiye.panel.person.musixmatch_id"),
					'ltr' => true
				),
				array(
					'name' => 'name_prefix',
					'label' => translator::trans("ghafiye.panel.person.name_prefix")
				),
				array(
					'name' => 'first_name',
					'label' => translator::trans("ghafiye.panel.person.first_name")
				),
				array(
					'name' => 'middle_name',
					'label' => translator::trans("ghafiye.panel.person.middle_name")
				),
				array(
					'name' => 'last_name',
					'label' => translator::trans("ghafiye.panel.person.last_name")
				),
				array(
					'name' => 'name_suffix',
					'label' => translator::trans("ghafiye.panel.person.name_suffix")
				),
				array(
					'name' => 'word',
					'label' => translator::trans("ghafiye.panel.person.word.key")
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
		<button type="submit" form="personSearch" class="btn btn-success"><?php echo translator::trans("search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cancel'); ?></button>
	</div>
</div>
<?php
$this->the_footer();
