<?php
namespace packages\ghafiye\logs\groups;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel\{logs\panel, logs};
use \packages\ghafiye\group;
class edit extends logs{
	public function getColor():string{
		return "circle-teal";
	}
	public function getIcon():string{
		return "fa fa-users";
	}
	public function buildFrontend(view $view){
		$parameters = $this->log->parameters;
		$oldData = $parameters['oldData'];
		$titles = isset($oldData['titles']) ? $oldData['titles'] : [];
		$persons = isset($oldData['persons']) ? $oldData['persons'] : [];
		unset($oldData['titles'], $oldData['persons']);

		if(!empty($oldData)){
			$panel = new panel('ghafiye.logs.group.edit');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.group.information');
			$html = '';
			$html .= '<div class="form-group">';
			$html .= '<label class="col-xs-4 control-label">'.translator::trans("ghafiye.panel.group.lang").': </label>';
			$html .= '<div class="col-xs-8 ltr">'.translator::trans("translations.langs.{$oldData['group-lang']}").'</div>';
			$html .= "</div>";
			$panel->setHTML($html);
			$this->addPanel($panel);
		}

		if(!empty($titles)){
			$panel = new panel('ghafiye.logs.group.edit.titles');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.group.titles');
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
		if(!empty($persons)){
			$panel = new panel('ghafiye.logs.group.edit.persons');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.group.persons');
			$html = '<div class="table-responsive">';
			$html .= '<table class="table table-striped">';
			$html .= "<thead><tr>";
			$html .= "<th>#</th>";
			$html .= "<th>نام شخص</th>";
			$html .= "</tr></thead>";
			$html .= "<tbody>";
			foreach($persons as $person){
				$html .= "<tr><td>{$person->person->id}</th>";
				$html .= "<td>{$person->person->name()}</td></tr>";
			}
			$html .= "</tbody></table></div>";
			$panel->setHTML($html);
			$this->addPanel($panel);
		}
	}
}
