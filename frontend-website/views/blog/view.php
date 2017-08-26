<?php
namespace themes\musixmatch\views\blog;
use \packages\blog;
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
		$this->setTitle([
			translator::trans("blog.list.title"),
			$this->post->title
		]);
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
					$code .= '<span> : </span> <a href="'.$category->getUrl().'" rel="category tag">'.$category->title.'</a>';
				}
			}else{
				$code = '<a class="dotted-link1" href="'.$categories[0]->getUrl().'" rel="category tag">'.$categories[0]->title.'</a>';
			}
		}
		return $code;
	}
	protected function getShareSocial(){
		return [
			[
				"name"=> "facebook",
				"link"=> "http://www.facebook.com/sharer.php?u=".$this->post->getURL([],true)
			],
			[
				"name"=> "telegram",
				"link"=> "tg://msg_url?text=".($this->post->title)."&url=".$this->post->getURL([],true)
			],
			[
				"name"=> "twitter",
				"link"=> "http://www.twitter.com/share?url=".$this->post->getURL([],true)
			],
			[
				"name"=> "google-plus",
				"link"=> "http://www.plus.google.com/sharer?url=".$this->post->getURL([],true)
			],
			[
				"name"=> "pinterest",
				"link"=> "http://www.pinterest.com/pin/create/button/?url=".$this->post->getURL([],true)
			],
			[
				"name"=> "linkedin",
				"link"=> "http://www.linkedin.com/shareArticle?mini=true&title=".$this->post->getURL([],true)
			],
			[
				"name"=> "tumblr",
				"link"=> "http://www.tumblr.com/share/link?url=".$this->post->getURL([],true)
			],
			[
				"name"=> "vk",
				"link"=> "http://www.vk.com/share.php?url=".$this->post->getURL([],true)
			],
			[
				"name"=> "reddit",
				"link"=> "http://www.reddit.com/submit?url=".$this->post->getURL([],true)
			],
			[
				"name"=> "mail",
				"link"=> "mailto:?subject={$this->post->title}&body={$this->post->description}\n".$this->post->getURL([],true)
			]
		];
	}
}
