<?php
use packages\userpanel;
use themes\clipone\utility;
use packages\userpanel\date;
use packages\base\translator;
use packages\ghafiye\Contribute;

$this->the_header();
?>
<?php if ($this->contributes) { ?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-wrench"></i> <?php echo translator::trans("ghafiye.panel.contributes"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans("search"); ?>" href="#search" data-toggle="modal" title=""><i class="fa fa-search"></i></a>
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
								<th><?php echo translator::trans("ghafiye.panel.contributes.title"); ?></th>
								<?php if ($this->childrenTypes) { ?>
								<th><?php echo translator::trans("ghafiye.panel.contributes.user"); ?></th>
								<?php } ?>
								<th><?php echo translator::trans("ghafiye.panel.contributes.doneAt"); ?></th>
								<th><?php echo translator::trans("ghafiye.panel.contributes.status"); ?></th>
								<?php if ($hasButtons) { ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->contributes as $contribute){
								$this->setButtonParam("view", "link", userpanel\url("contributes/view/" . $contribute->id));
								$this->setButtonParam("accept", "link", userpanel\url("contributes/accept/" . $contribute->id));
								$this->setButtonParam("reject", "link", userpanel\url("contributes/reject/" . $contribute->id));
								$this->setButtonParam("delete", "link", userpanel\url("contributes/delete/" . $contribute->id));
								$this->setButtonActive("accept", $contribute->status == Contribute::waitForAccept);
								$this->setButtonActive("reject", $contribute->status == Contribute::waitForAccept);
								$statusClass = utility::switchcase($contribute->status, array(
									"label label-success" => Contribute::accepted,
									"label label-warning" => Contribute::waitForAccept,
									"label label-inverse" => Contribute::rejected,
								));
								$statusTxt = utility::switchcase($contribute->status, array(
									"ghafiye.panel.contributes.status.accepted" => Contribute::accepted,
									"ghafiye.panel.contributes.status.waitForAccept" => Contribute::waitForAccept,
									"ghafiye.panel.contributes.status.rejected" => Contribute::rejected,
								));
							?>
							<tr>
								<td><?php echo $contribute->id; ?></td>
								<td><?php echo $contribute->title; ?></td>
								<?php if ($this->childrenTypes) { ?>
								<td>
									<a href="<?php echo userpanel\url("users", array("id" => $contribute->user->id)); ?>">
										<?php echo $contribute->user->getFullName(); ?>
									</a>
								</td>
								<?php } ?>
								<td class="ltr"><?php echo date::format("Y/m/d H:i", $contribute->done_at); ?></td>
								<td><span class="<?php echo $statusClass; ?>"><?php echo translator::trans($statusTxt); ?></span></td>
								<?php
								if ($hasButtons) {
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
		<h4 class="modal-title"><?php echo translator::trans("search"); ?></h4>
	</div>
	<div class="modal-body">
		<form id="ContributesSearchForm" class="form-horizontal" action="<?php echo userpanel\url("contributes"); ?>" method="GET" autocomplete="off">
			<?php
			$this->setHorizontalForm("sm-3","sm-9");
			$feilds = array(
				array(
					"name" => "id",
					"type" => "number",
					"label" => translator::trans("ghafiye.panel.contributes.id"),
					"ltr" => true
				),
				array(
					"type" => "select",
					"label" => translator::trans("ghafiye.panel.contributes.status"),
					"name" => "status",
					"options" => $this->getStatusForSelect(),
				)
			);
			if ($this->childrenTypes) {
				array_splice($feilds, 1, 0, array (
						array(
							"name" => "song",
							"type" => "hidden"
						),
						array(
							"name" => "song_name",
							"label" => translator::trans("ghafiye.panel.contributes.song"),
						),
						array(
							"name" => "user",
							"type" => "hidden"
						),
						array(
							"name" => "user_name",
							"label" => translator::trans("ghafiye.panel.contributes.user"),
						),
					)
				);
			}
			foreach ($feilds as $input) {
				$this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="ContributesSearchForm" class="btn btn-success"><?php echo translator::trans("search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans("cancel"); ?></button>
	</div>
</div>
<?php
	}
$this->the_footer();
