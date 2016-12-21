<?php
namespace themes\musixmatch\views;
use \packages\base;
use \packages\base\options;
use \packages\base\packages;
use \packages\blog\blogpost;

use \packages\userpanel\date;

trait blogTrait{
	protected $popular = array();
	protected $blog = array();
	public function popular_blog(){
		$new = new blogpost();
		$new->orderBy('view', 'DESC');
		return $new->get('4');
	}
	public function archive_box(){

		$blogposts = blogpost::orderBy('date', 'DESC')->ArrayBuilder()->get(null, array('date'));
		$arcive = array();
		foreach($blogposts as $blogpost){
			$arcive[] = date::mktime(0, 0, 0, date::format('m', $blogpost['date']), 1,date::format('Y', $blogpost['date']));
		}
		return array_unique($arcive);
	}
	function blogImage(blogpost $post){
		return (packages::package('blog')->url($post->image ? $post->image : options::get('packages.blog.defaultimage')));
	}
}
