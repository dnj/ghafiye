<?php
namespace packages\ghafiye\views\panel\songs\lyrics\descriptions;
use packages\ghafiye\{views\form, song\lyric\Description};

class Edit extends form {
	public function setLyricDesctription(Description $description) {
		$this->setData($description, "lyricdescription");
		$this->setDataForm($description->toArray());
	}
	protected function getLyricDescription() {
		return $this->getData("lyricdescription");
	}
}
