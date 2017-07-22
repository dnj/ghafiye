<?php
namespace themes\musixmatch\views\blog;
use \packages\base;
use \packages\base\translator;
use \packages\userpanel\date;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\blogTrait;
use \themes\musixmatch\views\formTrait;
use \packages\blog\comment;
use \packages\blog\views\post\view as postView;
class view extends postView{
	use viewTrait, blogTrait, formTrait;
	protected $post;
	private $number = 1;
	function __beforeLoad(){
		$this->post = $this->getPost();
		$this->setTitle(array(
			translator::trans("blog.list.title"),
			$this->post->title
		));
		$this->addBodyClass('article');
		$this->addBodyClass('blog');
	}
	protected function revertReply($reply = null){
		$html = "";
		$comments = $this->post->comments;
		$replyText = translator::trans("blog.post.comment.reply");
		foreach($comments as $comment){
			if($comment->reply == $reply and $comment->status == comment::accepted){
				$gravatar = 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($comment->email))).'?s=50&r=G&d='.urlencode("http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=50");
				$date = date::format("l j F Y",  $comment->date);
				if($comment->reply === null){
					$this->number = 1;
					$html .= "<article class=\"post\">";
				}else{
					$html .= "<article class=\"post col-md-offset-{$this->number}\">";
					$this->number++;
				}
				$html .= "<div class=\"row\"><div class=\"col-md-1\">";
				$html .= "<img src=\"{$gravatar}\"></div>";
				$html .= "<div class=\"col-md-11\">";
				if($comment->site){
					$html .= '<header><p class="h3"><a target="_blank" href="http://'.($comment->site ? $comment->site : '#').'">'.$comment->name.'</a></p>';
				}else{
					$html .= '<header><p class="h3">'.$comment->name.'</p>';
				}
				$html .= "<ul class=\"post-meta\">";
				$html .= "<li><i class=\"fa fa-clock-o\"></i>{$date}</li>";
				$html .= "<li><a class=\"artist reply\" data-comment=\"{$comment->id}\" href=\"#\"><i class=\"fa fa-undo\"></i>{$replyText}</a></li>";
				$html .= "</ul></header>";
				$html .= "<div class=\"post-content\">{$comment->text}</div>";
				$html .= "</div></div></article>";
				$html .= $this->revertReply($comment->id);
			}
		}
		return $html;
	}
	protected function showCategories(){
		$categories = $this->getCategories();
		$code = '';
		if(!empty($categories)){

			if(count($categories) > 1){
				foreach($categories as $category){
					$code .= '<span> : </span> <a href="'.base\url("blog/categories/".$category->id).'" rel="category tag">'.$category->title.'</a>';
				}
			}else{
				$code = '<a class="dotted-link1" href="'.base\url("blog/categories/".$categories[0]->id).'" rel="category tag">'.$categories[0]->title.'</a>';
			}
		}
		return $code;
	}
}
