<?php
namespace packages\ghafiye\contributes\songs;
use packages\base;
use packages\base\translator;
use packages\ghafiye\{Contributes, song};

class add extends Contributes {
	protected $point = 10;
	public function getPoint():int {
		return $this->point;
	}
	public function getImage(int $width, int $height): string {
		return $this->contribute->song->getImage($width, $height);
	}
	public function getPreviewContent(): string {
		return "<a href=\"{$this->contribute->song->url()}\">{$this->contribute->song->title()}</a>
		<a href=\"" . base\url($this->contribute->song->getSinger()->encodedName()) . "\" class=\"song-singer\">{$this->contribute->song->getSinger()->name()}</a>";
	}
	public function buildFrontend(): string {
		$isLtr = $this->isLtr($this->contribute->song->lang);
		$html = '<div class="row">
			<div class="col-sm-4 col-xs-12">
				<div class="row">
					<div class="col-xs-12">
						<img src="' . $this->getImage(250, 250) . '" alt="' . $this->contribute->song->title() . '" class="img-responsive">
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group">
							<label class="col-xs-5">' . translator::trans("ghafiye.song.title") . ':</label>
							<div class="col-xs-7' . ($isLtr ? " ltr" : "") .'">' . $this->contribute->song->title($this->contribute->song->lang) . '</div>
						</div>
						<div class="form-group">
							<label class="col-xs-5">' . translator::trans("ghafiye.singer") . ':</label>
							<div class="col-xs-7">' . $this->contribute->song->getSinger()->name() . '</div>
						</div>';
				if ($this->contribute->song->album) {
				$html .= '<div class="form-group">
							<label class="col-xs-5">' . translator::trans("ghafiye.album") . ':</label>
							<div class="col-xs-7' . ($this->isLtr($this->contribute->song->album->lang) ? " ltr" : "") .'">' . $this->contribute->song->album->title() . '</div>
						</div>';
				}
				if ($this->contribute->song->genre) {
			$html .= '<div class="form-group">
						<label class="col-xs-5">' . translator::trans("ghafiye.song.genre") . ':</label>
						<div class="col-xs-7">' . $this->contribute->song->genre->title() . '</div>
					</div>';
				}
			$html .= '</div>
				</div>
			</div>
			<div class="col-sm-8">';
		foreach ($this->getSongLyrics() as $lyric) {
			$html .= '<p class="lyric'. ($isLtr ? " ltr" : "") . '">';
				$html .= $lyric->text;
			$html .= '</p>';
		}
	$html .= '</div>
		</div>';
		return $html;
	}
	protected function getSongLyrics(): array {
		$lyric = new song\lyric();
		$lyric->where("song", $this->contribute->song->id);
		$lyric->where("lang", $this->contribute->song->lang);
		return $lyric->get();
	}
}