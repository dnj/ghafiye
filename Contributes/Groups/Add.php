<?php
namespace packages\ghafiye\contributes\groups;
use packages\base;
use packages\base\translator;
use packages\ghafiye\{Contributes, group};

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
}