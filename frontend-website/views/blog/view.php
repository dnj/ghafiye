<?php
namespace themes\musixmatch\views\blog;
use \packages\base;
use \packages\base\translator;
use \packages\base\frontend\theme;

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
		$this->addBodyClass('lyric');
		$this->addAsseste();
	}
	private function addAsseste(){
		$this->addCSSFile(theme::url("assets/css/styles-453111948d9c7c5c7ceb.css"));
		$this->addCSSFile(theme::url("assets/css/blog.css"));
		$this->addJSFile(theme::url("assets/js/pages/blog.view.js"));
		$this->addJSFile(theme::url("assets/plugins/jquery.growl/javascripts/jquery.growl.js"));
		$this->addCSSFile(theme::url("assets/plugins/jquery.growl/stylesheets/jquery.growl.css"));
	}
	protected function revertReply($reply = null){
		$html = "";
		$comments = $this->post->comments;
		$replyText = translator::trans("blog.post.comment.reply");
		foreach($comments as $comment){
			if($comment->reply == $reply and $comment->status == comment::accepted){
				$gravatar = 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($comment->email))).'?s=50&r=G&d='.urlencode("http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=50");
				$date = date::format("l j F Y",  $comment->date);
				$html .= "<li>";
				if($comment->reply === null){
					$this->number = 1;
					$html .= '<div class="activity-item show-avatar-icon track_translation">';
				}else{
					$html .= "<div class=\"activity-item show-avatar-icon track_translation col-md-offset-{$this->number}\">";
					$this->number++;
				}
				$html .= "<div class=\"activity-type\"></div><div class=\"container\"><div class=\"avatar\"><a class=\"profile-pic\">";
				$html .= "<img class=\"avatar-icon avatar-icon\" src=\"{$gravatar}\">";
				$html .= "</a></div></div>";
				$html .= "<div class=\"list\"><div class=\"track-card media-card has-picture\">";
				$html .= "<div class=\"media-card-body\"><h4 class=\"media-card-subtitle comment-name\">{$comment->name}</h4>{$comment->text}<div class=\"media-card-text\"><h3 class=\"media-card-subtitle\"><span class=\"artist-field\">";
				$html .= "<span><a class=\"artist reply\" data-comment=\"{$comment->id}\" href=\"#\">{$replyText}</a></span>";
				$html .= "</span></h3></div></div></div></div>";
				$html .= "<span class=\"date\">{$date}</span></div></li>";
				$html .= $this->revertReply($comment->id);
			}
		}
		return $html;
	}
	protected function showCategories(){
		$categories = $this->getCategories();
		$code = '';
		if(count($categories) > 1){
			foreach($categories as $category){
				$code .= '<span> : </span> <a href="'.base\url("blog/categories/".$category->id).'" rel="category tag">'.$category->title.'</a>';
			}
		}else{
			$code = '<a class="dotted-link1" href="'.base\url("blog/categories/".$categories[0]->id).'" rel="category tag">'.$categories[0]->title.'</a>';
		}
		return $code;
	}
}
