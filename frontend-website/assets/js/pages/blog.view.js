var blogViewPost = function(){
	var runReplyComment = function(){
		$(".reply").click(function(e){
			e.preventDefault();
			$('html, body').animate({ scrollTop: $(".setcomment").offset().top }, 2000);
			$("input[name=reply]").val($(this).data("comment"));
			var inreply = $(this).parents("header").find("h2").text();
			$(".reply-info-name").html("در پاسخ به: "+inreply);
			$(".reply-info").show();
		});
		$(".cancel-reply").click(function(e){
			e.preventDefault();
			$("input[name=reply]").val("");
			$(".reply-info").hide();
		});
	};
	var commentSendResult = function(){
		if($("#success").length){
			$.growl.notice({title:"متشکریم!", message:"نظر شما بعد از تایید توسط تیم ما نمایش داده خواهد شد"});
		}
		if($(".has-error").length){
			$.growl.error({title:"خطایی وجود دارد!", message:"لطفا خطاهای موجود را برطرف کنید و مجددا اقدام نمایید"});
		}
	}
	return{
		init:function(){
			runReplyComment();
			commentSendResult();
		}
	}
}();
$(function(){
	blogViewPost.init();
});
