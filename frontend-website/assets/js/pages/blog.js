var blogListView = function(){
	var mouseEventPosts = function(){
		$(".image").mouseover(function(){
			$(this).find(".continue").show();
		});
		$(".image").mouseout(function(){
			$(this).find(".continue").hide();
		});
	};
	return{
		init:function(){
			mouseEventPosts();
		}
	}
}();
$(function(){
	blogListView.init();
})
