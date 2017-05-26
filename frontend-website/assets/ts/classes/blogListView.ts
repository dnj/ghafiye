import * as $ from "jquery";
export class blogListView {
	public static mouseEventPosts():void{
		$(".image").mouseover(function(){
			$(this).find(".continue").show();
		}).mouseout(function(){
			$(this).find(".continue").hide();
		});
	}
	public static init():void{
		this.mouseEventPosts();
	}
}