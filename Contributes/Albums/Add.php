<?php
namespace packages\ghafiye\contributes\albums;
use packages\base;
use packages\base\{translator, db};
use packages\ghafiye\{Contributes, song, album, Contribute};

class Add extends Contributes {
	protected $point = 5;
	public function getPoint():int {
		return $this->point;
	}
	public function getImage(int $width, int $height): string {
		return $this->contribute->album->getImage($width, $height);
	}
	public function getPreviewContent(): string {
		$song = new song();
		$song->where("album", $this->contribute->album->id);
		if ($song = $song->getOne()) {
			return "<a href=\"" . base\url($song->getSinger()->encodedName($this->contribute->lang) . '/albums/' . $this->contribute->album->encodedTitle($this->contribute->lang)) . "\">{$this->contribute->album->title($this->contribute->lang)}</a>";
		}
		return '<a href="#">' . $this->contribute->album->title($this->contribute->lang) . "</a>";
	}
	public function buildFrontend(): string {
		$html = '<div class="row">
			<div class="col-sm-4 col-xs-12">
				<img class="img-responsive" src="' . $this->contribute->album->getImage(250, 250) . '" alt="' . $this->contribute->album->title($this->contribute->lang) . '">
			</div>
			<div class="col-sm-8 col-xs-12">
				<div class="form-group">
					<label class="col-xs-5">' . translator::trans("ghafiye.album.title") .':</label>
					<div class="col-xs-7">' . $this->contribute->album->title($this->contribute->lang) . '</div>
				</div>
				<div class="form-group">
					<label class="col-xs-5">' . translator::trans("ghafiye.album.lang") .':</label>
					<div class="col-xs-7">' . translator::trans("translations.langs.{$this->contribute->lang}") . '</div>
				</div>
			</div>
		</div>';
		return $html;
	}
	public function onAccept() {
		$this->contribute->album->status = album::accepted;
		$this->contribute->album->save();
		db::where("ghafiye_albums_titles.album", $this->contribute->album->id);
		db::where("ghafiye_albums_titles.lang", $this->contribute->lang);
		db::update("ghafiye_albums_titles", array(
			"status" => album\title::published,
		));
		$this->contribute->status = Contribute::accepted;
		$this->contribute->save();
		$this->contribute->user->points += $this->point;
		$this->contribute->user->save();
	}
	public function onReject() {
		$this->contribute->album->delete();
		$this->contribute->status = Contribute::rejected;
		$this->contribute->save();
	}
	public function onDelete() {
		$this->contribute->album->delete();
	}
}