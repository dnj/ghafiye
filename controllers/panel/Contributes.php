<?php
namespace packages\ghafiye\controllers\panel;
use packages\userpanel;
use packages\base\{response, db, NotFound};
use packages\userpanel\controller;
use packages\ghafiye\{view, views, authentication, authorization, Contribute, contributes\preventRejectException, contributes\preventDeleteException};

class Contributes extends controller {
	protected $authentication = true;
	public function search(): response {
		authorization::haveOrFail("contributes_search");
		$view = view::byName(views\panel\contributes\Search::class);
		$this->response->setView($view);
		$types = authorization::childrenTypes();
		$contribute = new Contribute();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_contributes.user", "INNER");
		if ($types) {
			$contribute->where("userpanel_users.type", $types, "in");
		} else {
			$contribute->where("userpanel_users.id", authentication::getID());
		}
		$inputsRules = array(
			"id" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"user" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"song" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"person" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"album" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"groupID" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"status" => array(
				"values" => array(Contribute::accepted, Contribute::waitForAccept, Contribute::rejected),
				"optional" => true,
				"empty" => true,
			),
		);
		$inputs = $this->checkinputs($inputsRules);
		foreach (array("id", "user", "status", "song", "album", "person", "groupID") as $item) {
			if (isset($inputs[$item]) and $inputs[$item]) {
				$contribute->where("ghafiye_contributes.{$item}", $inputs[$item]);
			}
		}
		$contribute->orderBy("done_at", "DESC");
		$contribute->pageLimit = $this->items_per_page;
		$contributes = $contribute->paginate($this->page, "ghafiye_contributes.*");
		$view->setDataList($contributes);
		$view->setPaginate($this->page, db::totalCount(), $this->items_per_page);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function accept($data): response {
		authorization::haveOrFail("contributes_edit");
		$types = authorization::childrenTypes();
		$contribute = new Contribute();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_contributes.user", "INNER");
		if ($types) {
			$contribute->where("userpanel_users.type", $types, "in");
		} else {
			$contribute->where("userpanel_users.id", authentication::getID());
		}
		$contribute->where("ghafiye_contributes.id", $data["contribute"]);
		$contribute->where("ghafiye_contributes.status", Contribute::waitForAccept);
		if (!$contribute = $contribute->getOne("ghafiye_contributes.*")) {
			throw new NotFound();
		}
		$view = view::byName(views\panel\contributes\Accept::class);
		$this->response->setView($view);
		$view->setContribute($contribute);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function onAccept($data) {
		authorization::haveOrFail("contributes_edit");
		$types = authorization::childrenTypes();
		$contribute = new Contribute();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_contributes.user", "INNER");
		if ($types) {
			$contribute->where("userpanel_users.type", $types, "in");
		} else {
			$contribute->where("userpanel_users.id", authentication::getID());
		}
		$contribute->where("ghafiye_contributes.id", $data["contribute"]);
		$contribute->where("ghafiye_contributes.status", Contribute::waitForAccept);
		if (!$contribute = $contribute->getOne("ghafiye_contributes.*")) {
			throw new NotFound();
		}
		$view = view::byName(views\panel\contributes\Accept::class);
		$this->response->setView($view);
		$view->setContribute($contribute);
		$contribute->accepted();
		$this->response->setStatus(true);
		$this->response->Go(userpanel\url("contributes"));
		return $this->response;
	}
	public function reject($data): response {
		authorization::haveOrFail("contributes_edit");
		$types = authorization::childrenTypes();
		$contribute = new Contribute();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_contributes.user", "INNER");
		if ($types) {
			$contribute->where("userpanel_users.type", $types, "in");
		} else {
			$contribute->where("userpanel_users.id", authentication::getID());
		}
		$contribute->where("ghafiye_contributes.id", $data["contribute"]);
		$contribute->where("ghafiye_contributes.status", Contribute::waitForAccept);
		if (!$contribute = $contribute->getOne("ghafiye_contributes.*")) {
			throw new NotFound();
		}
		$view = view::byName(views\panel\contributes\Reject::class);
		$this->response->setView($view);
		$view->setContribute($contribute);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function onReject($data) {
		authorization::haveOrFail("contributes_edit");
		$types = authorization::childrenTypes();
		$contribute = new Contribute();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_contributes.user", "INNER");
		if ($types) {
			$contribute->where("userpanel_users.type", $types, "in");
		} else {
			$contribute->where("userpanel_users.id", authentication::getID());
		}
		$contribute->where("ghafiye_contributes.id", $data["contribute"]);
		$contribute->where("ghafiye_contributes.status", Contribute::waitForAccept);
		if (!$contribute = $contribute->getOne("ghafiye_contributes.*")) {
			throw new NotFound();
		}
		$view = view::byName(views\panel\contributes\Reject::class);
		$this->response->setView($view);
		$view->setContribute($contribute);
		try {
			$contribute->rejected();
			$this->response->setStatus(true);
			$this->response->Go(userpanel\url("contributes"));
		} catch (preventRejectException $exception) {
			foreach ($exception->getErrors() as $error) {
				$view->addError($error);
			}
		}
		return $this->response;
	}
	public function delete($data): response {
		authorization::haveOrFail("contributes_delete");
		$types = authorization::childrenTypes();
		$contribute = new Contribute();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_contributes.user", "INNER");
		if ($types) {
			$contribute->where("userpanel_users.type", $types, "in");
		} else {
			$contribute->where("userpanel_users.id", authentication::getID());
		}
		$contribute->where("ghafiye_contributes.id", $data["contribute"]);
		if (!$contribute = $contribute->getOne("ghafiye_contributes.*")) {
			throw new NotFound();
		}
		$view = view::byName(views\panel\contributes\Delete::class);
		$this->response->setView($view);
		$view->setContribute($contribute);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function terminate($data) {
		authorization::haveOrFail("contributes_delete");
		$types = authorization::childrenTypes();
		$contribute = new Contribute();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_contributes.user", "INNER");
		if ($types) {
			$contribute->where("userpanel_users.type", $types, "in");
		} else {
			$contribute->where("userpanel_users.id", authentication::getID());
		}
		$contribute->where("ghafiye_contributes.id", $data["contribute"]);
		if (!$contribute = $contribute->getOne("ghafiye_contributes.*")) {
			throw new NotFound();
		}
		$view = view::byName(views\panel\contributes\Delete::class);
		$this->response->setView($view);
		$view->setContribute($contribute);
		try {
			$contribute->delete();
			$this->response->setStatus(true);
			$this->response->Go(userpanel\url("contributes"));
		} catch (preventDeleteException $exception) {
			foreach ($exception->getErrors() as $error) {
				$view->addError($error);
			}
		}
		return $this->response;
	}
	public function view($data): response {
		authorization::haveOrFail("contributes_view");
		$types = authorization::childrenTypes();
		$contribute = new Contribute();
		db::join("userpanel_users", "userpanel_users.id=ghafiye_contributes.user", "INNER");
		if ($types) {
			$contribute->where("userpanel_users.type", $types, "in");
		} else {
			$contribute->where("userpanel_users.id", authentication::getID());
		}
		$contribute->where("ghafiye_contributes.id", $data["contribute"]);
		$contribute->where("ghafiye_contributes.status", Contribute::rejected, "!=");
		if (!$contribute = $contribute->getOne("ghafiye_contributes.*")) {
			throw new NotFound();
		}
		$view = view::byName(views\panel\contributes\View::class);
		$this->response->setView($view);
		$view->setContribute($contribute);
		$this->response->setStatus(true);
		return $this->response;
	}
}
