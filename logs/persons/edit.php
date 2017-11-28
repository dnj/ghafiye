<?php
namespace packages\ghafiye\logs\persons;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel;
use \packages\userpanel\{date, logs\panel, logs};
use \packages\ghafiye\person;
class edit extends logs{
	public function getColor():string{
		return "circle-teal";
	}
	public function getIcon():string{
		return "fa fa-user";
	}
	private function getGenderTranslate(int $gender){
		switch($gender){
			case(person::men):
				return translator::trans("ghafiye.panel.person.gender.men");
			case(person::women):
				return translator::trans("ghafiye.panel.person.gender.women");
		}
	}
	public function buildFrontend(view $view){
		$parameters = $this->log->parameters;
		$oldData = $parameters['oldData'];
		$names = isset($oldData['names']) ? $oldData['names'] : [];
		unset($oldData['names']);

		if(!empty($oldData)){
			$panel = new panel('ghafiye.logs.person.edit');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.person.information');
			$html = '';
			if(isset($oldData['gender'])){
				$html .= '<div class="form-group">';
				$html .= '<label class="col-xs-4 control-label">'.translator::trans("ghafiye.panel.person.gender").': </label>';
				$html .= '<div class="col-xs-8 ltr">'.$this->getGenderTranslate($oldData['gender']).'</div>';
				$html .= "</div>";
				unset($oldData['gender']);
			}
			foreach($oldData as $field => $val){
				$html .= '<div class="form-group">';
				$html .= '<label class="col-xs-4 control-label">'.translator::trans("ghafiye.panel.person.{$field}").': </label>';
				$html .= '<div class="col-xs-8 ltr">'.$val.'</div>';
				$html .= "</div>";
			}
			$panel->setHTML($html);
			$this->addPanel($panel);
		}

		if(!empty($names)){
			$panel = new panel('ghafiye.logs.person.edit.titles');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('ghafiye.logs.person.titles');
			$html = '<div class="table-responsive">';
			$html .= '<table class="table table-striped">';
			$html .= "<thead><tr>";
			$html .= "<th>#</th>";
			$html .= "<th>زبان</th>";
			$html .= "<th>عنوان</th>";
			$html .= "</tr></thead>";
			$html .= "<tbody>";
			foreach($names as $name){
				$html .= "<tr><td>{$name->id}</th>";
				$html .= "<td>".translator::trans("translations.langs.{$name->lang}")."</td>";
				$html .= "<td>{$name->name}</td></tr>";
			}
			$html .= "</tbody></table></div>";
			$panel->setHTML($html);
			$this->addPanel($panel);
		}
	}
}
