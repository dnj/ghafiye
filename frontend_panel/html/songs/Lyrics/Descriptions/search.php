<?php
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\date;
use \packages\ghafiye\song\lyric\Description;
use \themes\clipone\utility;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
	<?php if ($this->descriptions) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-question"></i> <?php echo translator::trans("ghafiye.panel.songs.lyrics.descriptions"); ?>
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
								<th><?php echo translator::trans("ghafiye.panel.song.lyrics.descriptions.song"); ?></th>
								<th><?php echo translator::trans("ghafiye.panel.song.lyrics.descriptions.user"); ?></th>
								<th><?php echo translator::trans("ghafiye.panel.song.lyrics.descriptions.sent_at"); ?></th>
								<th><?php echo translator::trans("ghafiye.panel.song.lyrics.descriptions.status"); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->descriptions as $description){
								$this->setButtonParam("edit", "link", userpanel\url("songs/lyrics/descriptions/edit/" . $description->id));
								$this->setButtonParam("delete", "link", userpanel\url("songs/lyrics/descriptions/delete/" . $description->id));
								$statusClass = utility::switchcase($description->status, array(
									"label label-success" => Description::accepted,
									"label label-warning" => Description::waitForAccept,
									"label label-inverse" => Description::rejected,
								));
								$statusTxt = utility::switchcase($description->status, array(
									"ghafiye.panel.song.lyrics.descriptions.status.accepted" => Description::accepted,
									"ghafiye.panel.song.lyrics.descriptions.status.waitForAccept" => Description::waitForAccept,
									"ghafiye.panel.song.lyrics.descriptions.status.rejected" => Description::rejected,
								));
							?>
							<tr>
								<td><?php echo $description->id; ?></td>
								<td><?php echo $description->lyric->song->title(); ?></td>
								<td><?php echo $description->user->getFullName(); ?></td>
								<td class="ltr"><?php echo date::format("Y/m/d H:i", $description->sent_at); ?></td>
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
	<?php } ?>
</div>
<div class="modal fade" id="search" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans("search"); ?></h4>
	</div>
	<div class="modal-body">
		<form id="DescriptionsSearchForm" class="form-horizontal" action="<?php echo userpanel\url("songs/lyrics/descriptions"); ?>" method="GET" autocomplete="off">
			<?php
			$this->setHorizontalForm("sm-3","sm-9");
			$feilds = array(
				array(
					"name" => "id",
					"type" => "number",
					"label" => translator::trans("ghafiye.panel.song.id"),
					"ltr" => true
				),
				array(
					"name" => "song",
					"type" => "hidden"
				),
				array(
					"name" => "song_name",
					"label" => translator::trans("ghafiye.panel.song.lyrics.descriptions.song"),
				),
				array(
					"type" => "select",
					"label" => translator::trans("ghafiye.panel.song.lyrics.descriptions.status"),
					"name" => "status",
					"options" => $this->getStatusForSelect(),
				),
				array(
					"name" => "word",
					"label" => translator::trans("ghafiye.panel.song.word.key"),
					"ltr" => true
				),
				array(
					"type" => "select",
					"label" => translator::trans("search.comparison"),
					"name" => "comparison",
					"options" => $this->getComparisonsForSelect()
				)
			);
			if ($this->hasChildrenType) {
				array_splice($feilds, 2, 0, array(
					array(
						"name" => "user",
						"type" => "hidden"
					),
					array(
						"name" => "user_name",
						"label" => translator::trans("ghafiye.panel.song.lyrics.descriptions.user"),
					),
				));
			}
			foreach ($feilds as $input) {
				$this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="DescriptionsSearchForm" class="btn btn-success"><?php echo translator::trans("search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans("cancel"); ?></button>
	</div>
</div>
<?php
$this->the_footer();
