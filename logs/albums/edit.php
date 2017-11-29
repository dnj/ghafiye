<?php
namespace packages\ghafiye\logs\albums;
use \packages\base\{view, translator};
use \packages\userpanel\{logs\panel, logs};
class edit extends logs{
	public function getColor():string{
		return "circle-teal";
	}
	public function getIcon():string{
		return "fa fa-file-audio-o";
	}
	public function buildFrontend(view $view){
		$parameters = $this->log->parameters;
		$oldData = $parameters['oldData'];
		$titles = isset($oldData['titles']) ? $oldData['titles'] : [];
		$songs = isset($oldData['songs']) ? $oldData['songs'] : [];
		unset($oldData['titles'], $oldData['songs']);

		if(!empty($oldData)){
			$panel = new panel('ghafiye.logs.album.edit');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.album.information');
			$html = '';
			if(isset($oldData['album-lang'])){
				$html .= '<div class="form-group">';
				$html .= '<label class="col-xs-4 control-label">'.translator::trans("ghafiye.panel.album.lang").': </label>';
				$html .= '<div class="col-xs-8 ltr">'.translator::trans("translations.langs.{$oldData['album-lang']}").'</div>';
				$html .= "</div>";
			}
			if(isset($oldData['musixmatch_id'])){
				$html .= '<div class="form-group">';
				$html .= '<label class="col-xs-4 control-label">'.translator::trans("ghafiye.panel.album.musixmatch_id").': </label>';
				$html .= '<div class="col-xs-8 ltr">'.$oldData['musixmatch_id'].'</div>';
				$html .= "</div>";
			}
			$panel->setHTML($html);
			$this->addPanel($panel);
		}

		if(!empty($titles)){
			$panel = new panel('ghafiye.logs.album.edit.titles');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.album.titles');
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
		if(!empty($songs)){
			$panel = new panel('ghafiye.logs.album.edit.songs');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.album.songs');
			$html = '<div class="table-responsive">';
			$html .= '<table class="table table-striped">';
			$html .= "<thead><tr>";
			$html .= "<th>#</th>";
			$html .= "<th>عنوان</th>";
			$html .= "</tr></thead>";
			$html .= "<tbody>";
			foreach($songs as $song){
				$html .= "<tr><td>{$song->id}</th>";
				$html .= "<td>{$song->title()}</td></tr>";
			}
			$html .= "</tbody></table></div>";
			$panel->setHTML($html);
			$this->addPanel($panel);
		}
	}
}
