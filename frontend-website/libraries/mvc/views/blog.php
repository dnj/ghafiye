<?php
namespace themes\musixmatch\views;
use \packages\base;
use \packages\base\options;
use \packages\base\packages;
use \packages\blog\post;
use \packages\userpanel\date;
use \packages\blog\category;
trait blogTrait{
	protected $popular = array();
	protected $blog = array();
	private $categories;
	function __construct(){
		$this->categories = category::get();
	}
	public function popular_blog():array{
		$new = new post();
		$new->where('status', post::published);
		$new->orderBy('view', 'DESC');
		return $new->get(4);
	}
	public function archive_box():array{
		$posts = post::where('status', post::published)->orderBy('date', 'DESC')->ArrayBuilder()->get(null, array('date'));
		$arcive = array();
		foreach($posts as $post){
			$arcive[] = date::mktime(0, 0, 0, date::format('m', $post['date']), 1,date::format('Y', $post['date']));
		}
		return array_unique($arcive);
	}
	function blogImage(post $post, bool $absolute = false){
		return (packages::package('blog')->url($post->image ? $post->image : options::get('packages.blog.defaultimage'), $absolute));
	}
	public function getPostsSubJects($parent = null){
		$html = "";
		foreach($this->categories as $category){
			if($category->parent == $parent){
				$html .= "<li> <a href=\"".$category->getURL()."\">{$category->title}</a>";
				$children = $this->getPostsSubJects($category->id);
				if($children){
					$html .= "<ul class=\"child\">";
					$html .= $children;
					$html .= "</ul>";
				}
				$html .= "</li>";
			}
		}
		return $html;
	}
}
