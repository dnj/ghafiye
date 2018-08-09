<?php
namespace packages\ghafiye\contributes\persons;
use packages\base;
use packages\base\{db, view\error, translator};
use packages\ghafiye\{Contributes, person, song, Contribute, contributes\preventRejectException};

class Add extends Contributes {
	protected $point = 10;
	public function getPoint():int {
		return $this->point;
	}
	public function getImage(int $width, int $height): string {
		return $this->contribute->person->getImage($width, $height);
	}
	public function getPreviewContent(): string {
		return "<a href=\"" . base\url($this->contribute->person->encodedName()) . "\">{$this->contribute->person->name($this->contribute->lang)}</a>";
	}
	public function buildFrontend(): string {
		$html = '<div class="row">
			<div class="col-sm-4 col-xs-12">
				<img class="img-responsive" src="' . $this->contribute->person->getImage(250, 250) . '" alt="' . $this->contribute->person->name($this->contribute->lang) . '">
			</div>
			<div class="col-sm-8 col-xs-12">
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group">
							<label class="col-xs-5">' . translator::trans("ghafiye.person.name") .':</label>
							<div class="col-xs-7">' . $this->contribute->person->name($this->contribute->lang) . '</div>
						</div>
						<div class="form-group">
							<label class="col-xs-5">' . translator::trans("ghafiye.person.lang") .':</label>
							<div class="col-xs-7">' . translator::trans("translations.langs.{$this->contribute->lang}") . '</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
		return $html;
	}
	public function onAccept() {
		$this->contribute->person->status = person::accepted;
		$this->contribute->person->save();
		db::where("ghafiye_persons_names.person", $this->contribute->person->id);
		db::where("ghafiye_persons_names.lang", $this->contribute->lang);
		db::update("ghafiye_persons_names", array(
			"status" => person\name::published,
		));
		$this->contribute->status = Contribute::accepted;
		$this->contribute->save();
		$this->contribute->user->points += $this->point;
		$this->contribute->user->save();
	}
	public function onReject() {
		$person = new song\person();
		$person->where("person", $this->contribute->person->id);
		if ($person->has()) {
			$error = new error();
			$error->setType(error::FATAL);
			$error->setCode("ghafiye.contributes.persons.add.onReject");
			throw new preventRejectException($error);
		}
		$this->contribute->person->delete();
		$this->contribute->status = Contribute::rejected;
		$this->contribute->save();
	}
	public function onDelete() {
		$this->contribute->person->delete();
	}
}