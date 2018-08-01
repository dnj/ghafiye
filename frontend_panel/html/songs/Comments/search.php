<?php
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\date;
use \packages\ghafiye\song\Comment;
use \themes\clipone\utility;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
	<?php if ($this->comments) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-comments"></i> <?php echo translator::trans("ghafiye.panel.songs.comments"); ?>
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
								<th><?php echo translator::trans("ghafiye.panel.songs.comments.song"); ?></th>
								<th><?php echo translator::trans("ghafiye.panel.songs.comments.name"); ?></th>
								<th><?php echo translator::trans("ghafiye.panel.songs.comments.replyTo"); ?></th>
								<th><?php echo translator::trans("ghafiye.panel.songs.comments.email"); ?></th>
								<th><?php echo translator::trans("ghafiye.panel.songs.comments.sent_at"); ?></th>
								<th><?php echo translator::trans("ghafiye.panel.song.comments.status"); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->comments as $comment){
								$this->setButtonParam("view", "link", userpanel\url("songs/comments/view/" . $comment->id));
								$this->setButtonParam("delete", "link", userpanel\url("songs/comments/delete/" . $comment->id));
								$statusClass = utility::switchcase($comment->status, array(
									"label label-success" => Comment::accepted,
									"label label-warning" => Comment::waitForAccept,
									"label label-inverse" => Comment::rejected,
								));
								$statusTxt = utility::switchcase($comment->status, array(
									"ghafiye.panel.song.comments.status.accept" => Comment::accepted,
									"ghafiye.panel.song.comments.status.waitForAccept" => Comment::waitForAccept,
									"ghafiye.panel.song.comments.status.rejected" => Comment::rejected,
								));
							?>
							<tr>
								<td><?php echo $comment->id; ?></td>
								<td><?php echo $comment->song->title(); ?></td>
								<td><?php echo $comment->name; ?></td>
								<td class="center"><?php echo $comment->reply ? '<a href=' . userpanel\url("songs/comments", array("id" => $comment->reply)) . '">#' . $comment->reply . '</a>' : "-"; ?></td>
								<td><?php echo $comment->email; ?></td>
								<td class="ltr"><?php echo date::format("Y/m/d H:i", $comment->sent_at); ?></td>
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
		<form id="CommentsSearchForm" class="form-horizontal" action="<?php echo userpanel\url("songs/comments"); ?>" method="GET" autocomplete="off">
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
					"label" => translator::trans("ghafiye.panel.songs.comments.song"),
				),
				array(
					"type" => "select",
					"label" => translator::trans("ghafiye.panel.song.comments.status"),
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
			foreach ($feilds as $input) {
				$this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="CommentsSearchForm" class="btn btn-success"><?php echo translator::trans("search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans("cancel"); ?></button>
	</div>
</div>
<?php
$this->the_footer();
