<?php
namespace themes\musixmatch\views;
use \packages\base;
use \packages\base\options;
use \packages\base\packages;
use \packages\blog\post;

use \packages\userpanel\date;

trait blogTrait{
	protected $popular = array();
	protected $blog = array();
	public function popular_blog(){
		$new = new post();
		$new->orderBy('view', 'DESC');
		return $new->get(4);
	}
	public function archive_box(){

		$posts = post::orderBy('date', 'DESC')->ArrayBuilder()->get(null, array('date'));
		$arcive = array();
		foreach($posts as $post){
			$arcive[] = date::mktime(0, 0, 0, date::format('m', $post['date']), 1,date::format('Y', $post['date']));
		}
		return array_unique($arcive);
	}
	function blogImage(post $post){
		return (packages::package('blog')->url($post->image ? $post->image : options::get('packages.blog.defaultimage')));
	}
}
