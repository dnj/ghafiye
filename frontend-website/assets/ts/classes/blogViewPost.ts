import * as $ from "jquery";
import "jquery.growl";
import "bootstrap/js/tooltip"
export class blogViewPost{
	public static runReplyComment(): void{
		$(".reply").on('click',function(e){
			e.preventDefault();
			$('html, body').animate({ scrollTop: $(".setcomment").offset().top }, 2000);
			$("input[name=reply]").val($(this).data("comment"));
			let inreply = $(this).parents("header").find("h2").text();
			$(".reply-info-name").html("در پاسخ به: "+inreply);
			$(".reply-info").show();
		});
		$(".cancel-reply").on('click',function(e){
			e.preventDefault();
			$("input[name=reply]").val("");
			$(".reply-info").hide();
		});
	}
	public static commentSendResult(){
		if($("#success").length){
			$.growl.notice({title:"متشکریم!", message:"نظر شما بعد از تایید توسط تیم ما نمایش داده خواهد شد"});
		}
		if($(".has-error").length){
			$.growl.error({title:"خطایی وجود دارد!", message:"لطفا خطاهای موجود را برطرف کنید و مجددا اقدام نمایید"});
		}
	}
	public static init(){
		this.runReplyComment();
		this.commentSendResult();
		$('.share-box-list a.tooltips').tooltip();
	}
}