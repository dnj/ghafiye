import {HomePageSearch} from "../classes/HomePageSearch";
import {viewLyric} from "../classes/viewLyric";
import {blogListView} from "../classes/blogListView";
import {blogViewPost} from "../classes/blogViewPost";
$(function(){
	let body = $('body');
	HomePageSearch.init();
	if(body.hasClass('blog')){
		if(body.hasClass('list')){
			blogListView.init();
		}else{
			blogViewPost.init();
		}
	}
	if(body.hasClass('lyric')){
		viewLyric.init();
	}
});