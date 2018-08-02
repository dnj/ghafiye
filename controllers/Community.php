<?php
namespace packages\ghafiye\controllers;
use packages\base\{db, response};
use packages\ghafiye\{view, views, controller, Contribute};

class Community extends controller{
	public function contributes($data): response {
		$view = view::byName(views\community\Contributes::class);
		$contribute = new Contribute();
		$contribute->orderBy("done_at", "DESC");
		$contribute->where("status", Contribute::accepted);
		$contribute->pageLimit = $this->items_per_page;
		$contributes = $contribute->paginate($this->page);
		$view->setContrbutes($contributes);
		$view->setPaginate($this->page, db::totalCount(), $contribute->pageLimit);
		$view->setWeeklyUsersLeaderboard(Contribute::getWeeklyUsersLeaderboard());
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
}
