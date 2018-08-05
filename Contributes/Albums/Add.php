<?php
namespace packages\ghafiye\contributes\albums;
use packages\base;
use packages\base\translator;
use packages\ghafiye\{Contributes, song};

class Add extends Contributes {
	protected $point = 10;
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
}