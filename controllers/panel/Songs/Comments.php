<?php
namespace packages\ghafiye\controllers\panel\songs;
use packages\base\{db, NotFound, view\error, inputValidation, views\FormError, response};
use packages\userpanel;
use packages\userpanel\{controller, log};
use packages\ghafiye\{view, views, authorization, authentication, song};

class Comments extends controller {
	protected $authentication = true;
	public function search(): response {
		authorization::haveOrFail("songs_comments_search");
		$view = view::byName(views\panel\songs\comments\Search::class);
		$comment = new song\Comment();
		$inputsRules = array(
			"id" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"song" => array(
				"type" => "number",
				"optional" => true,
				"empty" => true,
			),
			"status" => array(
				"values" => array(song\Comment::accepted, song\Comment::waitForAccept, song\Comment::rejected),
				"optional" => true,
				"empty" => true,
			),
			"word" => array(
				"type" => "string",
				"optional" => true,
				"empty" => true,
			),
			"comparison" => array(
				"values" => array("equals", "startswith", "contains"),
				"default" => "contains",
				"optional" => true,
			),
		);
		try{
			$inputs = $this->checkinputs($inputsRules);
			foreach (array("id", "song", "status") as $item) {
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs["comparison"];
					if(in_array($item, array("id", "song", "status"))){
						$comparison = "equals";
					}
					$comment->where($item, $inputs[$item], $comparison);
				}
			}
			if (isset($inputs["word"]) and $inputs["word"]) {
				$comment->where("content", $inputs["word"], $inputs["comparison"]);
			}
			$comment->pageLimit = $this->items_per_page;
			$comment->orderBy("sent_at", "DESC");
			$comments = $comment->paginate($this->page);
			$this->total_pages = $comment->totalPages;
			$view->setDataList($comments);
			$view->setPaginate($this->page, db::totalCount(), $this->items_per_page);
			$this->response->setStatus(true);
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function view($data): response {
		authorization::haveOrFail("songs_comments_view");
		$view = view::byName(views\panel\songs\comments\View::class);
		if (!$comment = song\Comment::byId($data["comment"])) {
			throw new NotFound();
		}
		$view->setComment($comment);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data): response {
		authorization::haveOrFail("songs_comments_edit");
		$view = view::byName(views\panel\songs\comments\View::class);
		if (!$comment = song\Comment::byId($data["comment"])) {
			throw new NotFound();
		}
		$view->setComment($comment);
		$inputsRules = array(
			"status" => array(
				"values" => array(song\Comment::accepted, song\Comment::waitForAccept, song\Comment::rejected),
				"optional" => true,
			),
		);
		try {
			$inputs = $this->checkinputs($inputsRules);
			if (isset($inputs["status"])) {
				$comment->status = $inputs["status"];
			}
			$comment->save();
			$this->response->Go(userpanel\url("songs/comments/view/{$comment->id}"));
			$this->response->setStatus(true);
		} catch (inputValidation $error) {
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data): response {
		authorization::haveOrFail("songs_comments_delete");
		$view = view::byName(views\panel\songs\comments\Delete::class);
		if (!$comment = song\Comment::byId($data["comment"])) {
			throw new NotFound();
		}
		$view->setComment($comment);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function terminate($data): response {
		authorization::haveOrFail("songs_comments_delete");
		$view = view::byName(views\panel\songs\comments\Delete::class);
		if (!$comment = song\Comment::byId($data["comment"])) {
			throw new NotFound();
		}
		$comment->delete();
		$this->response->Go(userpanel\url("songs/comments"));
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
}