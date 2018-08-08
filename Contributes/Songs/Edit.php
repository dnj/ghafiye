<?php
namespace packages\ghafiye\contributes\songs;
use packages\base;
use packages\base\{db, translator, packages};
use packages\ghafiye\{Contributes, contribute\Lyric, song, Contribute};

class Edit extends Contributes {
	protected $point = 5;
	protected $lyrics = array();
	protected $checkeDeletedLyrics = false;
	protected $deletedLyrics = array();
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
		$html = '<div class="row">
				<div class="col-sm-4 col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<img src="' . $this->getSongImage() . '" alt="' . $this->getTitle() . '" class="img-responsive">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<label class="col-xs-5">' . translator::trans("ghafiye.song.title") . ':</label>
								<div class="col-xs-7' . ($isLtr ? " ltr" : "") .'">' . $this->getTitle() . '</div>
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
				<div class="col-sm-8 col-xs-12">';
		foreach ($this->getSongLyrics() as $lyric) {
			$html .= '<div class="panel">
						<div class="panel-body">
							<div class="row">
								<div class="col-xs-12">';
									$html .= $this->getLyricHtml($lyric, $isLtr);
						$html .= '</div>
							</div>
						</div>
					</div>';
		}
		$html .= '</div>
			</div>';
		return $html;
	}
	public function onAccept() {
		$deletedLyrics = $this->contribute->param("deletedLyrics");
		$lyric = new Lyric();
		$lyric->where("contribute", $this->contribute->id);
		foreach ($lyric->get() as $lyr) {
			if ($lyric = $lyr->lyric) {
				if ($deletedLyrics) {
					if (in_array($lyr->id, $deletedLyrics)) {
						$lyric->delete();
						continue;
					}
				}
				$lyric->text = $lyr->text;
				$lyric->status = song\lyric::published;
				$lyric->ordering = $lyr->ordering;
				$lyric->save();
			}
		}
		if ($title = $this->contribute->param("title")) {
			$this->contribute->song->setTitle($title, $this->contribute->song->lang);
		}
		if ($img = $this->contribute->param("image")) {
			$this->contribute->song->image = $img;
		}
		$this->contribute->song->save();
		$lyric = new song\lyric();
		$lyric->where("song", $this->contribute->song->id);
		$lyric->where("status", song\lyric::published);
		$lyric->orderBy("ordering", "ASC");
		$i = 1;
		foreach ($lyric->get() as $lyric) {
			$lyric->ordering = $i++;
			$lyric->save();
		}
		$this->contribute->status = Contribute::accepted;
		$this->contribute->save();
		$this->contribute->user->points += $this->point;
		$this->contribute->user->save();
	}
	public function onReject() {
		db::join("ghafiye_contributes_lyrics", "ghafiye_contributes_lyrics.lyric=ghafiye_songs_lyrices.id", "INNER");
		$lyric = new song\lyric();
		$lyric->where("ghafiye_contributes_lyrics.contribute", $this->contribute->id);
		$lyric->where("ghafiye_songs_lyrices.status", song\lyric::draft);
		$lyrics = $lyric->get(null, "ghafiye_songs_lyrices.*");
		foreach ($lyrics as $lyr) {
			$lyric->delete();
		}
		$this->contribute->status = Contribute::rejected;
		$this->contribute->save();
	}
	public function onDelete() {
		db::join("ghafiye_contributes_lyrics", "ghafiye_contributes_lyrics.lyric=ghafiye_songs_lyrices.id", "INNER");
		$lyric = new song\lyric();
		$lyric->where("ghafiye_contributes_lyrics.contribute", $this->contribute->id);
		$lyric->where("ghafiye_songs_lyrices.status", song\lyric::draft);
		$lyrics = $lyric->get();
		foreach ($lyric->get() as $lyr) {
			$lyric->delete();
		}
	}
	protected function getSongLyrics(): array {
		$lyric = new song\lyric();
		$lyric->where("song", $this->contribute->song->id);
		$lyric->where("lang", $this->contribute->song->lang);
		$lyric->where("status", song\lyric::published);
		$lyric->orderBy("ordering", "ASC");
		$lyrics = $lyric->get();
		$orderingUpLyrics = $this->contribute->param("orderingUpLyrics");
		if (!$orderingUpLyrics) {
			$orderingUpLyrics = array();
		}
		$orderingDownLyrics = $this->contribute->param("orderingDownLyrics");
		if (!$orderingDownLyrics) {
			$orderingDownLyrics = array();
		}
		foreach ($this->getContributeLyrics() as $lyr) {
			if ($lyr->lyric and $lyr->ordering) {
				if ($lyr->lyric->status == song\lyric::published) {
					foreach ($lyrics as $lyric) {
						if ($lyric->id == $lyr->lyric->id) {
							if (in_array($lyr->id, $orderingUpLyrics)) {
								$lyric->up = true;
							} else if (in_array($lyr->id, $orderingDownLyrics)) {
								$lyric->down = true;
							}
							$lyric->ordering = $lyr->ordering;
						}
					}
				} else {
					$lyrics[] = $lyr->lyric;
				}
			} else {
				$tmp = new song\lyric();
				$tmp->text = $lyr->text;
				$tmp->ordering = $lyr->ordering;
				$tmp->deleted = true;
				$lyrics[] = $tmp;
			}
		}
		usort($lyrics, function($a, $b) {
			return $a->ordering - $b->ordering;
		});
		return $lyrics;
	}
	protected function getContributeLyrics(): array {
		if (!$this->lyrics) {
			$lyric = new Lyric();
			$lyric->where("contribute", $this->contribute->id);
			$this->lyrics = $lyric->get();
		}
		return $this->lyrics;
	}
	protected function getLyricHtml(song\lyric $lyric, bool $isLtr): string {
		if (!$this->deletedLyrics and !$this->checkeDeletedLyrics) {
			$this->checkeDeletedLyrics = true;
			$this->deletedLyrics = $this->contribute->param("deletedLyrics");
		}
		if (!$this->deletedLyrics) {
			$this->deletedLyrics = array();
		}
		foreach ($this->getContributeLyrics() as $lyr) {
			if ($lyr->lyric and $lyr->lyric->id == $lyric->id) {
				$html = "";
				if ($lyr->old_text) {
					$html .= '<del class="lyric'. ($isLtr ? " ltr" : "") . '">' . $lyr->old_text . '</del>';
				}
				if (in_array($lyr->id, $this->deletedLyrics)) {
					$html .= '<del class="lyric'. ($isLtr ? " ltr" : "") . '">' . $lyr->text . '</del>';
				} else {
					if ($lyric->up) {
						$html .= '<i class="fa fa-arrow-up"></i>';
					} else if ($lyric->down) {
						$html .= '<i class="fa fa-arrow-down"></i>';
					}
					$html .= '<ins class="lyric'. ($isLtr ? " ltr" : "") . '">' . $lyr->text . '</ins>';
				}
				return $html;
			} else if ($lyric->deleted) {
				return '<del class="lyric'. ($isLtr ? " ltr" : "") . '">' . $lyr->text . '</del>';
			}
		}
		return '<p class="lyric'. ($isLtr ? " ltr" : "") . '">' . $lyric->text . '</p>';
	}
	protected function getTitle(): string {
		$title = $this->contribute->param("title");
		return $title ? $title : $this->contribute->song->title($this->contribute->lang);
	}
	protected function getSongImage(): string {
		$img = $this->contribute->param("image");
		if ($img) {
			return packages::package("ghafiye")->url($img);
		}
		return $this->getImage(250, 250);
	}
}