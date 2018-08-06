<?php
namespace packages\ghafiye\contributes\persons;
use packages\base;
use packages\base\{db, view\error};
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
		$html = "";
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
}