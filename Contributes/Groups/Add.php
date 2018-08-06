<?php
namespace packages\ghafiye\contributes\groups;
use packages\base;
use packages\base\{translator, db, view\error};
use packages\ghafiye\{Contributes, group, song, Contribute, contributes\preventRejectException};

class Add extends Contributes {
	protected $point = 10;
	public function getPoint():int {
		return $this->point;
	}
	public function getImage(int $width, int $height): string {
		return $this->contribute->group->getImage($width, $height);
	}
	public function getPreviewContent(): string {
		return "<a href=\"" . base\url($this->contribute->group->encodedName()) . "\">{$this->contribute->group->name($this->contribute->lang)}</a>";
	}
	public function buildFrontend(): string {
		$html = '<div class="row">
			<div class="col-sm-4 col-xs-12">
				<img class="img-responsive" src="' . $this->contribute->group->getImage(250, 250) . '" alt="' . $this->contribute->group->title($this->contribute->lang) . '">
			</div>
			<div class="col-sm-8 col-xs-12">
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group">
							<label class="col-xs-5">' . translator::trans("ghafiye.group.name") .':</label>
							<div class="col-xs-7">' . $this->contribute->group->title($this->contribute->lang) . '</div>
						</div>
						<div class="form-group">
							<label class="col-xs-5">' . translator::trans("ghafiye.group.lang") .':</label>
							<div class="col-xs-7">' . translator::trans("translations.langs.{$this->contribute->lang}") . '</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-xs-12">
						<div class="table-responsive">
							<table class="table table-hover table-striped table-bordered">
								<thead>
									<tr>
										<th>' . translator::trans("ghafiye.singer.name") .'</th>
									</tr>
								</thead>
								<tbody>';
								foreach ($this->getGroupPersons() as $person) {
									$html .= '<tr><td>';
										$html .= '<a href="' . base\url($person->person->encodedName()) . '">' . $person->person->name($this->contribute->lang) . '</a>';
									$html .= '</td></tr>';
								}
						$html .= '</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>';
		return $html;
	}
	protected function getGroupPersons() {
		$person = new group\person();
		$person->where("group_id", $this->contribute->groupID);
		return $person->get();
	}
	public function onAccept() {
		$this->contribute->group->status = group::accepted;
		$this->contribute->group->save();
		db::where("ghafiye_groups_titles.group_id", $this->contribute->group->id);
		db::where("ghafiye_groups_titles.lang", $this->contribute->lang);
		db::update("ghafiye_groups_titles", array(
			"status" => group\title::published,
		));
		$this->contribute->status = Contribute::accepted;
		$this->contribute->save();
		$this->contribute->user->points += $this->point;
		$this->contribute->user->save();
	}
	public function onReject() {
		$song = new song();
		$song->where("`group`", $this->contribute->groupID);
		if ($song->has()) {
			$error = new error();
			$error->setType(error::FATAL);
			$error->setCode("ghafiye.contributes.groups.add.onReject");
			throw new preventRejectException($error);
		}
		$this->contribute->group->delete();
		$this->contribute->status = Contribute::rejected;
		return $this->contribute->save();
	}
}