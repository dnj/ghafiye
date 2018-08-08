<?php
namespace packages\ghafiye\contributes\songs;
use packages\base;
use packages\base\translator;
use packages\ghafiye\{Contributes, contribute\Lyric, song, Contribute};

class Translate extends Contributes {
	protected $point = 10;
	protected $translates;
	protected $lyrics;
	public function getPoint(): int {
		return $this->point;
	}
	public function getImage(int $width, int $height): string {
		return $this->contribute->song->getImage($width, $height);
	}
	public function getPreviewContent(): string {
		return "<a href=\"{$this->contribute->song->url()}\">{$this->contribute->song->title($this->contribute->lang)}</a>
		<a class=\"song-singer\" href=\"" . base\url($this->contribute->song->getSinger()->encodedName()) . "\" class=\"song-singer\">{$this->contribute->song->getSinger()->name()}</a>";
	}
	public function buildFrontend(): string {
		$isLtr = $this->isLtr($this->contribute->song->lang);
		$translteILtr = $this->isLtr($this->contribute->lang);
		$html = '<div class="row">
			<div class="col-sm-4 col-xs-12">
				<div class="row">
					<div class="col-xs-12">
						<img src="' . $this->getImage(250, 250) . '" alt="' . $this->getTitle() . '" class="img-responsive">
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group">
							<label class="col-xs-5">' . translator::trans("ghafiye.song.title") . ':</label>
							<div class="col-xs-7' . ($translteILtr ? " ltr" : "") .'">' . $this->getTitle() . '</div>
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
				$html .= '<div class="form-group">
							<label class="col-xs-5">' . translator::trans("ghafiye.song.genre") . ':</label>
							<div class="col-xs-7">' . $this->contribute->song->genre->title() . '</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-8">';
		foreach ($this->getSongLyrics() as $lyric) {
			$html .= '<div class="panel">';
				$html .= '<div class="panel-body">';
					$html .= '<p class="lyric'. ($isLtr ? " ltr" : "") . '">';
						$html .= $lyric->text;
					$html .= '</p>';
					$html .= $this->getTranslateLyric($lyric->id);
				$html .= '</div>';
			$html .= '</div>';
		}
	$html .= '</div>
		</div>';
		return $html;
	}
	protected function getSongLyrics(): array {
		$lyric = new song\lyric();
		$lyric->where("song", $this->contribute->song->id);
		$lyric->where("lang", $this->contribute->song->lang);
		$lyric->where("status", song\lyric::published);
		return $lyric->get();
	}
	protected function getTranslateLyric(int $id): string {
		$translteILtr = $this->isLtr($this->contribute->lang);
		if (!$this->translates) {
			$lyric = new song\lyric();
			$lyric->where("song", $this->contribute->song->id);
			$lyric->where("lang", $this->contribute->lang);
			$lyric->where("status", song\lyric::published);
			$this->translates = $lyric->get();
		}
		if (!$this->lyrics) {
			$lyric = new Lyric();
			$lyric->where("contribute", $this->contribute->id);
			$this->lyrics = $lyric->get();
		}
		foreach ($this->lyrics as $lyric) {
			if ($lyric->parent == $id) {
				$html = "";
				if ($lyric->old_text) {
					$html = '<del class="lyric"'. ($translteILtr ? " ltr" : "") . '">' . $lyric->old_text . '</del>';
				}
				$html .= '<ins class="lyric'. ($translteILtr ? " ltr" : "") . '">' . $lyric->text . '</ins>';
				return $html;
			}
		}
		foreach ($this->translates as $translat) {
			if ($translat->parent == $id) {
				return '<p class="lyric'. ($translteILtr ? " ltr" : "") . '">' . $translat->text . '</p>';
			}
		}
		return "";
	}
	public function onAccept() {
		$lyric = new Lyric();
		$lyric->where("contribute", $this->contribute->id);
		foreach ($lyric->get() as $lyr) {
			if ($lyr->lyric) {
				$lyr->lyric->text = $lyr->text;
				$lyr->lyric->status = song\lyric::published;
				$lyr->lyric->save();
			}
		}
		if ($title = $this->contribute->param("title")) {
			$this->contribute->song->setTitle($title, $this->contribute->lang);
		}
		$lyric = new song\lyric();
		$lyric->where("song", $this->song->id);
		$lyric->where("lang", $this->song->lang);
		$lyric->where("status", song\lyric::published);
		$countOrginalLyrics = $lyric->count();

		$lyric = new song\lyric();
		$lyric->where("song", $this->song->id);
		$lyric->where("lang", $this->contribute->lang);
		$lyric->where("status", song\lyric::published);
		$count = $lyric->count();

		$progress = ceil(($count * 100) / $countOrginalLyrics);
		$translate = new song\Translate();
		$translate->where("lang", $this->contribute->lang);
		if ($translate = $translate->getOne()) {
			$translate->progress = $progress > 100 ? 100 : $progress;
		} else {
			$translate = new song\Translate();
			$translate->song = $this->contribute->id;
			$translate->lang = $this->contribute->lang;
			$translate->progress = $progress > 100 ? 100 : $progress;
			$translate->save();
		}
		$this->contribute->status = Contribute::accepted;
		$this->contribute->save();
		$this->contribute->user->points += $this->point;
		$this->contribute->user->save();
	}
	public function onReject() {
		$lyric = new Lyric();
		$lyric->where("contribute", $this->contribute->id);
		foreach ($lyric->get() as $lyr) {
			if ($lyr->lyric) {
				$lyr->lyric->delete();
			}
		}
		$this->contribute->status = Contribute::rejected;
		$this->contribute->save();
	}
	public function onDelete() {
		$lyric = new Lyric();
		$lyric->where("contribute", $this->contribute->id);
		foreach ($lyric->get() as $lyr) {
			if ($lyr->lyric) {
				$lyr->lyric->delete();
			}
		}
	}
	protected function getTitle(): string {
		$title = $this->contribute->param("title");
		return $title ? $title : $this->contribute->song->title($this->contribute->lang);
	}
}