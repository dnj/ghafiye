<?php
namespace themes\musixmatch\views\contributes\songs;
use packages\base\{translator, packages, options};
use themes\musixmatch\{viewTrait, views\formTrait};
use packages\ghafiye\{views\contributes\songs\Add as parentView, person};

class Add extends parentView {
	use viewTrait, formTrait;
	protected $image;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.contribute.add.song"));
		$this->addBodyClass("article");
		$this->addBodyClass("contribute");
		$this->addBodyClass("contribute-add-song");
		$this->image = packages::package("ghafiye")->url(options::get("packages.ghafiye.getImage.defaultImage"));
	}
	protected function getGenresForSelect(): array {
		$genres = array(
			array(
				"title" => translator::trans("ghafiye.choose"),
				"value" => "",
			),
		);
		foreach($this->getGenres() as $genre){
			$genres[] = array(
				"title" => $genre->title("fa"),
				"value" => $genre->id
			);
		}
		return $genres;
	}
	protected function getLangsForSelect(): array {
		$langs = [];
		foreach(["fa", "en"] as $lang){
			$langs[] = array(
				"title" => translator::trans("translations.langs.{$lang}"),
				"value" => $lang
			);
		}
		foreach(translator::$allowlangs as $lang){
			if(in_array($lang, ["en", "fa"])){
				continue;
			}
			$langs[] = array(
				"title" => translator::trans("translations.langs.{$lang}"),
				"value" => $lang
			);
		}
		return $langs;
	}
	protected function getImage() {
		return $this->image;
	}
	protected function getGendesFroSelect() {
		return array(
			array(
				"title" => translator::trans("ghafiye.choose"),
				"value" => "",
			),
			array(
				"title" => translator::trans("ghafiye.gender.men"),
				"value" => person::men,
			),
			array(
				"title" => translator::trans("ghafiye.gender.women"),
				"value" => person::women,
			),
		);
	}
}
