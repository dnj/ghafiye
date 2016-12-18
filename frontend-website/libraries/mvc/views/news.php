<?php
namespace themes\musixmatch\views;
use \packages\base;
use \packages\base\options;
use \packages\base\packages;
use \packages\news\newpost;

use \packages\userpanel\date;

trait newsTrait{
	protected $popular = array();
	protected $news = array();
	public function popular_news(){
		$new = new newpost();
		$new->orderBy('view', 'DESC');
		return $new->get('4');
	}
	public function archive_box(){

		$newposts = newpost::orderBy('date', 'DESC')->ArrayBuilder()->get(null, array('date'));
		$arcive = array();
		foreach($newposts as $newpost){
			$arcive[] = date::mktime(0, 0, 0, date::format('m', $newpost['date']), 1,date::format('Y', $newpost['date']));
		}
		return array_unique($arcive);
	}
	public function the_siderbar(){
		require_once(__DIR__.'/../../../sidebar.news.php');
	}
	function getImage(newpost $post){
		$newspackage = packages::package('news');
		return ($newspackage->url($post->image ? $post->image : options::get('packages.news.defaultimage')));
	}
}
