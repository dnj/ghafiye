import * as $ from "jquery";
import {HomePageSearch} from "../classes/HomePageSearch";
import {viewLyric} from "../classes/viewLyric";
import {blogListView} from "../classes/blogListView";
import {blogViewPost} from "../classes/blogViewPost";
import Contributes from "../classes/Contributes";
import "bootstrap";
import Contribute from "../classes/Contribute";

declare const packages_ghafiye_isLogin: any;

export default class Main {
	public static windowWidth: number;
	public static windowHeight: number;
	protected static _isLogin: boolean;
	public static init() {
		Main.windowWidth = $(window).width();
		Main.windowHeight = $(window).height();
		let $body = $('body');
		HomePageSearch.init();
		if($body.hasClass('blog')){
			if($body.hasClass('list')){
				blogListView.init();
			}else{
				blogViewPost.init();
			}
		}
		if($body.hasClass('lyric')){
			viewLyric.init();
		}
		$('a[data-toggle=tab]', $body).on('click', function(e){
			e.preventDefault();
			$(this).tab('show')
		});
		Contributes.initIfNeeded();
		$(window).on("resize", () => {
			Main.windowWidth = $(window).width();
			Main.windowHeight = $(window).height();
		});
		Main._isLogin = packages_ghafiye_isLogin;
		Contribute.initIfNeeded();
		$(".tooltips").tooltip({
			container: "body",
		});
	}
	static get isLogin() {
		return Main._isLogin;
	}
}
$(function(){
	Main.init();
});