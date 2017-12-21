<?php
namespace packages\ghafiye\logs\songs;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel;
use \packages\userpanel\{date, logs\panel, logs};
use \packages\ghafiye\{song, song\person};
class edit extends logs{
	public function getColor():string{
		return "circle-teal";
	}
	public function getIcon():string{
		return "fa fa-music";
	}
	private function getRoleTranslate(person $person){
		switch($person->role){
			case(person::singer):
				return translator::trans("ghafiye.panel.song.person.role.singer");
			case(person::writer):
				return translator::trans("ghafiye.panel.song.person.role.writer");
			case(person::composer):
				return translator::trans("ghafiye.panel.song.person.role.composer");
		}
	}
	public function buildFrontend(view $view){
		$parameters = $this->log->parameters;
		$oldData = $parameters['oldData'];
		$persons = isset($oldData['persons']) ? $oldData['persons'] : [];
		$titles = isset($oldData['titles']) ? $oldData['titles'] : [];
		$lyrics = isset($oldData['lyrics']) ? $oldData['lyrics'] : [];
		unset($oldData['persons'], $oldData['titles'], $oldData['lyrics']);

		if(!empty($oldData)){
			$panel = new panel('ghafiye.logs.song.edit');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.song.information');
			$html = '';
			var_dump($oldData);
			var_dump(isset($oldData['release_at']));
			if (isset($oldData['release_at'])) {
				$html .= '<div class="form-group">';
				$html .= '<label class="col-xs-4 control-label">'.translator::trans("ghafiye.panel.song.release_at").': </label>';
				$html .= '<div class="col-xs-8 ltr">'.date::format("Y/m/d H:i:s", $oldData['release_at']).'</div>';
				$html .= "</div>";
				unset($oldData['release_at']);
			}
			if (array_key_exists("update_at", $oldData)) {
				$html .= '<div class="form-group">';
				$html .= '<label class="col-xs-4 control-label">'.translator::trans("ghafiye.panel.song.update_at").': </label>';
				$html .= '<div class="col-xs-8 ltr">'.date::format("Y/m/d H:i:s", $oldData['update_at']).'</div>';
				$html .= "</div>";
				unset($oldData['update_at']);
			}
			if (isset($oldData['album'])) {
				$html .= '<div class="form-group">';
				$html .= '<label class="col-xs-4 control-label">'.translator::trans("ghafiye.panel.song.album").': </label>';
				$html .= '<div class="col-xs-8 ltr">'.$oldData['album']->title().'</div>';
				$html .= "</div>";
				unset($oldData['album']);
			}
			foreach($oldData as $field => $val){
				$html .= '<div class="form-group">';
				$html .= '<label class="col-xs-4 control-label">'.translator::trans("ghafiye.panel.song.{$field}").': </label>';
				$html .= '<div class="col-xs-8 ltr">'.$val.'</div>';
				$html .= "</div>";
			}
			$panel->setHTML($html);
			$this->addPanel($panel);
		}

		if(!empty($persons)){
			$panel = new panel('ghafiye.logs.song.edit.persons');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.song.persons');
			$html = '<div class="table-responsive">';
			$html .= '<table class="table table-striped">';
			$html .= "<thead><tr>";
			$html .= "<th>#</th>";
			$html .= "<th>نام شخص</th>";
			$html .= "<th>نقش</th>";
			$html .= "<th>اصلی؟</th>";
			$html .= "</tr></thead>";
			$html .= "<tbody>";
			foreach($persons as $person){
				$html .= "<tr><td>{$person->person->id}</th>";
				$html .= "<td>{$person->person->name()}</td>";
				$html .= "<td>{$this->getRoleTranslate($person)}</td>";
				$html .= "<td class=\"center\">".($person->primary ? '<i class="fa fa-check-square-o"></i>' : '<i class="fa fa-times"></i>')."</td></tr>";
			}
			$html .= "</tbody></table></div>";
			$panel->setHTML($html);
			$this->addPanel($panel);
		}
		if(!empty($titles)){
			$panel = new panel('ghafiye.logs.song.edit.titles');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.song.titles');
			$html = '<div class="table-responsive">';
			$html .= '<table class="table table-striped">';
			$html .= "<thead><tr>";
			$html .= "<th>#</th>";
			$html .= "<th>زبان</th>";
			$html .= "<th>عنوان</th>";
			$html .= "</tr></thead>";
			$html .= "<tbody>";
			foreach($titles as $title){
				$html .= "<tr><td>{$title->id}</th>";
				$html .= "<td>".translator::trans("translations.langs.{$title->lang}")."</td>";
				$html .= "<td>{$title->title}</td></tr>";
			}
			$html .= "</tbody></table></div>";
			$panel->setHTML($html);
			$this->addPanel($panel);
		}
		if(!empty($lyrics)){
			$panel = new panel('ghafiye.logs.song.edit.lyrics');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.song.lyrics');
			$html = '<div class="table-responsive">';
			$html .= '<table class="table table-striped">';
			$html .= "<thead><tr>";
			$html .= "<th>#</th>";
			$html .= "<th>زبان</th>";
			$html .= "<th>متن</th>";
			$html .= "</tr></thead>";
			$html .= "<tbody>";
			foreach($lyrics as $lyric){
				$html .= "<tr><td>{$lyric->id}</th>";
				$html .= "<td>".translator::trans("translations.langs.{$lyric->lang}")."</td>";
				$html .= "<td>{$lyric->text}</td></tr>";
			}
			$html .= "</tbody></table></div>";
			$panel->setHTML($html);
			$this->addPanel($panel);
		}
	}
}
