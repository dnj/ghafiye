<?php
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\ghafiye\crawler\queue;
use \themes\clipone\utility;
$this->the_header();
?>
<?php if(!empty($this->getCrawlerLists())){ ?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-bug"></i> <?php echo translator::trans("ghafiye.panle.crawler.queue"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('search'); ?>" href="#search" data-toggle="modal" data-original-title=""><i class="fa fa-search"></i></a>
					<?php if($this->canAdd){ ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('add'); ?>" href="<?php echo userpanel\url('crawler/queue/add'); ?>"><i class="fa fa-plus"></i></a>
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
								<th><?php echo translator::trans('ghafiye.panel.crawler.queue.type'); ?></th>
								<th><?php echo translator::trans('ghafiye.panel.crawler.queue.MMID'); ?></th>
								<th><?php echo translator::trans('ghafiye.panel.crawler.queue.status'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->getCrawlerLists() as $queue){
								$this->setButtonParam('edit', 'link', userpanel\url("crawler/queue/edit/".$queue->id));
								$this->setButtonParam('delete', 'link', userpanel\url("crawler/queue/delete/".$queue->id));
								$statusClass = utility::switchcase($queue->status, [
									'label label-success' => queue::passed,
									'label label-inverse' => queue::queued,
									'label label-primary' => queue::running,
									'label label-danger' => queue::faild
								]);
								$statusTxt = utility::switchcase($queue->status, [
									'ghafiye.panel.crawler.queue.status.passed' => queue::passed,
									'ghafiye.panel.crawler.queue.status.queued' => queue::queued,
									'ghafiye.panel.crawler.queue.status.running' => queue::running,
									'ghafiye.panel.crawler.queue.status.faild' => queue::faild
								]);
							?>
							<tr>
								<td class="center"><?php echo $queue->id; ?></td>
								<td><?php echo $this->getTypeTranslate($queue->type); ?></td>
								<td class="ltr"><?php echo $queue->MMID; ?></td>
								<td><span class="<?php echo $statusClass; ?>"><?php echo translator::trans($statusTxt); ?></span></td>
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
	<div class="modal fade" id="search" tabindex="-1" data-show="true" role="dialog">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?php echo translator::trans('search'); ?></h4>
		</div>
		<div class="modal-body">
			<form id="queuesLists" class="form-horizontal" action="<?php echo userpanel\url("crawler/queue"); ?>" method="GET" autocomplete="off">
				<?php
				$this->setHorizontalForm('sm-3','sm-9');
				$feilds = [
					[
						'name' => 'id',
						'type' => 'number',
						'label' => translator::trans("ghafiye.panel.crawler.queue.id"),
						'ltr' => true
					],
					[
						'name' => 'type',
						'type' => 'select',
						'label' => translator::trans("ghafiye.panel.crawler.queue.type"),
						'options' => $this->getTypesForSelect()
					],
					[
						'name' => 'MMID',
						'type' => 'number',
						'label' => translator::trans("ghafiye.panel.crawler.queue.MMID"),
						'ltr' => true
					],
					[
						'type' => 'select',
						'label' => translator::trans('ghafiye.panel.crawler.queue.status'),
						'name' => 'status',
						'options' => $this->getStatusesForSelect()
					],
					[
						'name' => 'word',
						'label' => translator::trans('ghafiye.panel.crawler.queue.word')
					],
					[
						'type' => 'select',
						'label' => translator::trans('search.comparison'),
						'name' => 'comparison',
						'options' => $this->getComparisonsForSelect()
					]
				];
				foreach($feilds as $input){
					$this->createField($input);
				}
				?>
			</form>
		</div>
		<div class="modal-footer">
			<button type="submit" form="queuesLists" class="btn btn-success"><?php echo translator::trans("search"); ?></button>
			<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cancel'); ?></button>
		</div>
	</div>
</div>
<?php } ?>
<?php
$this->the_footer();
