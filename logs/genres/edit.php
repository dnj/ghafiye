<?php
namespace packages\ghafiye\logs\genres;
use \packages\base\{view, translator};
use \packages\userpanel\{logs\panel, logs};
class edit extends logs{
	public function getColor():string{
		return "circle-teal";
	}
	public function getIcon():string{
		return "fa fa-edit";
	}
	public function buildFrontend(view $view){
		$parameters = $this->log->parameters;
		$oldData = $parameters['oldData'];
		$titles = isset($oldData['titles']) ? $oldData['titles'] : [];
		unset($oldData['titles']);

		if(!empty($oldData)){
			$panel = new panel('ghafiye.logs.genre.edit');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.genre.information');
			$html = '<div class="form-group">';
			$html .= '<label class="col-xs-4 control-label">'.translator::trans("ghafiye.panel.genre.musixmatch_id").': </label>';
			$html .= '<div class="col-xs-8 ltr">'.$oldData['musixmatch_id'].'</div>';
			$html .= "</div>";
			$panel->setHTML($html);
			$this->addPanel($panel);
		}

		if(!empty($titles)){
			$panel = new panel('ghafiye.logs.genre.edit.titles');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.genre.titles');
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
	}
}
