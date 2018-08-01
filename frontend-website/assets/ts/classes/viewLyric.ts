import { Router, AjaxRequest } from "webuilder";
import "webuilder/formAjax";
import "bootstrap-inputmsg";
import "bootstrap";
import Main from "../events/main";

interface IDescription {
	id: number;
	likes: number;
	text: string;
	isLike: boolean,
	user: {
		id: number;
		name: string;
	};
	sent_at: {
		relative: string;
		date: string;
	},
}

export class viewLyric{
	public static init():void {
		viewLyric.song = parseInt($("#like").data("song") as string, 10);
		viewLyric.runLikelistener();
		$(".tooltips").tooltip();
		if($(".translations select.selectpicker").length){
			$(".translations select.selectpicker").val($("section.text").data("lang"));
			viewLyric.runChangeLangListener();
		}
		viewLyric.addCommentsubmitFormListener();
		viewLyric.runLyricsDescription();
		viewLyric.setCommentsReplyEvents();
	}
	protected static song: number;
	public static runLikelistener():void{
		$("#like").on("click", function(e){
			e.preventDefault();
			$.ajax({
				url: Router.url("songs/like/" + viewLyric.song),
				dataType: "json",
				data: {
					ajax: 1
				},
				success: function( data:{status?:boolean, liked:boolean} ) {
					if(data.hasOwnProperty("status")){
						if(data.status){
							let likes:number = parseInt($(".like-number").text());
							if(data.liked){
								$(".like-icon").attr("class", "fa fa-heart like-icon");
								$(".like-number").text(++likes);
							}else{
								$(".like-icon").attr("class", "fa fa-heart-o like-icon");
								$(".like-number").text(--likes);
							}
						}
					}
				}
			});
		});
	}
	private static runChangeLangListener():void{
		$(".translations select.selectpicker option").on("click", function(){
			if($(this).hasClass("ltr")){
				$(this).parents(".selectpicker").addClass("ltr");
			}else{
				$(this).parents(".selectpicker").removeClass("ltr");
			}
			const lang = $("section.text").data("lang");
			if(lang != $(this).val()){
				window.location.href = $(this).data("link");
			}
		});
	}
	protected static addCommentsubmitFormListener() {
		$(".comments-section .panel-comments form").on("submit", function(e) {
			e.preventDefault();
			const reset = () => {
				(this as HTMLFormElement).reset();
				$(".has-error", this).removeClass("has-error");
				$(".help-block", this).remove();
				$(".reply-section", this).slideUp();
			};
			($(this) as any).formAjax({
				url: `songs/${viewLyric.song}/addComment?ajax=1`,
				dataType: "json",
				success: (data: any) => {
					$.growl.notice({
						title:"موفق",
						message:"نظر شما بعد از تایید ما به نمایش در خواهد آمد."
					});
					reset();
				},
				error: function(error: any) {
					if(error.error == "data_duplicate" || error.error == "data_validation"){
						let $input = $(`[name="${error.input}"]`);
						let $params = {
							title: "خطا",
							message:""
						};
						if (error.error == "data_validation") {
							$params.message = "داده وارد شده معتبر نیست";
						}
						if (error.error == "data_duplicate") {
							$params.message = "داده وارد شده تکراری میباشد";
						}
						if ($input.length) {
							$input.inputMsg($params);
						} else {
							$.growl.error($params);
						}
					} else if (error.hasOwnProperty("message")) {
						const $error = `<div class="alert alert-block alert-danger ">
											<button data-dismiss="alert" class="close" type="button">×</button>
											<h4 class="alert-heading"><i class="fa fa-times-circle"></i> خطا</h4>
											<p>${error.message}</p>
										</div>`;
						$(".container .errors").html($error);
					} else {
						$.growl.error({
							title:"خطا",
							message:"درخواست شما توسط سرور قبول نشد"
						});
					}
				}
			});
		});
	}
	protected static runLyricsDescription() {
		let activePopover: JQuery;
		const $container = $(".lyric section.text");
		const $decs = $("span", $container);
		$decs.popover({
			placement: "bottom",
			html: true,
			content: `<div class="text-center"><i class="fa fa-spinner fa-pulse fa-2x fa-fw text-muted"></i><span>در حال دریافت</span></div>`,
		});
		$decs.on("show.bs.popover", function() {
			$(".popover .popover-content", $(this)).html(`<div class="text-center"><i class="fa fa-spinner fa-pulse fa-2x fa-fw text-muted"></i><span>در حال دریافت</span></div>`);
			$(".popover .popover-title", $(this)).html("توضیحات");
		});
		$decs.on("shown.bs.popover", function() {
			if (activePopover !== undefined && !activePopover.is($(this))) {
				activePopover.popover("hide");
			}
			activePopover = $(this);
			$(this).data("prevent-close", true);
			const parent = $(this).parent();
			const $clone = $(this).clone();
			$(".popover", $clone).remove();
			const lyric = parseInt($(this).data("lyric") as string, 10);
			const $popoverContainer = $(".popover .popover-content", parent);
			$popoverContainer.css("height", "");
			let html = "";
			if (Main.isLogin) {
				html = `<div class="text-center form-group"><button type="button" class="btn btn-sm btn-success btn-add-description">افزودن توضیح<i class="fa fa-pluse"></i></button></div>`;
			} else {
				html = `<p class="text-center form-group"><small>برای افزودن توضیحات <a href="${Router.url("userpanel/register")}">ثبت نام </a> کنید و یا <a href="${Router.url("userpanel/login")}">وارد</a> شوید</small></p>`;
			}
			AjaxRequest({
				url: `songs/lyric/${lyric}/description?ajax=1`,
				dataType: "json",
				method: "POST",
				success: function(data) {
					if (!data.items.length) {
						return;
					}
					for (const description of data.items as IDescription[]) {
						html += `<div class="panel panel-default">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-8"><span class="username">${description.user.name}</span></div>
									<div class="col-xs-4"><time class="tooltips" title="${description.sent_at.date}">${description.sent_at.relative}</time></div>
								</div>
							</div>
							<div class="panel-body">${description.text}</div>
							<div class="panel-footer">
								<div class="row">
									<div class="col-xs-4 pull-left">
										<button type="button" class="btn btn-sm ${description.isLike ? "btn-success" : "btn-default"} btn-like" data-description="${description.id}"><i class="fa fa-thumbs-o-up"></i> <span>${description.likes}</span> </button>
									</div>
								</div>
							</div>
						</div>`;
					}
					$popoverContainer.html(html);
					viewLyric.setDescriptionEvetns($(".popover", parent));
					viewLyric.setLyricPopoverEvents($(".popover", parent), $clone.text(), $clone.hasClass("ltr"), lyric);
				},
				error: function() {
					$popoverContainer.html(`${html}<div class="alert alert-info">توضیحی ثبت نشده است</div>`);
					$popoverContainer.css("height", "auto");
					viewLyric.setLyricPopoverEvents($(".popover", parent), $clone.text(), $clone.hasClass("ltr"), lyric);
				}
			});
		});
		$decs.on("hidden.bs.popover", function() {
			$("body").removeClass("modal-open");
		});
	}
	protected static setLyricPopoverEvents($popover: JQuery, text: string, isLtr: boolean, lyric: number) {
		if (!Main.isLogin) {
			return;
		}
		const $container = $(".popover-content", $popover);
		const song = $("header > .title h1").html();
		const singer = $("header > .title h2").html();
		$(".btn-add-description", $popover).on("click", () => {
			$(".popover-title", $popover).html("افزودن توضیح");
			if (Main.windowWidth < 768) {
				$popover.css({
					position: "fixed",
					top: 0,
					bottom: 0,
					margin: 0,
				});
				$("body").addClass("modal-open");
			}
			$container.html(`<form id="addLyricDescriptionForm" method="post">
				<p>با افزودن توضیحات میتوانید امتیاز کسب کنید</p>
				<p><strong>نام آهنگ:</strong> ${song}</p>
				<p><strong>اثر:</strong> ${singer}</p>
				<p><strong>متن عبارت:</strong></p>
				<p class="${isLtr ? "ltr text-left" : ""}">${text}</p>
				<div class="form-group">
					<label class="control-label">توضیح</label>
					<textarea rows="8" name="content" class="form-control"></textarea>
				</div>
			</form>
			<div class="popover-footer">
				<div class="row">
					<div class="col-xs-6">
						<button type="submit" class="btn btn-sm btn-block btn-success btn-submit" form="addLyricDescriptionForm"><i class="fa fa-check-square-o"></i> افزودن</button>
					</div>
					<div class="col-xs-6">
						<button type="button" class="btn btn-sm btn-block btn-default btn-close"><i class="fa fa-times"></i> بستن</button>
					</div>
				</div>
			</div>
			`);
			if (Main.windowWidth < 768) {
				$container.css("height", Main.windowHeight - ($(".popover-title", $popover).height() + $(".popover-footer").height() + 40));
			} else {
				$container.css("height", "auto");
			}
			$(".btn-close", $container).one("click", () => {
				$popover.parent().popover("hide").fadeOut();
			});
			$("form", $popover).on("submit", function(e) {
				e.preventDefault();
				const $btn = $(".btn-submit", $popover);
				$btn.prop("disabled", true);
				$("i", $btn).removeClass("fa-check-square-o").addClass("fa-spinner fa-pulse fa-fw");
				($(this) as any).formAjax({
					url: `songs/lyric/${lyric}/description/add?ajax=1`,
					dataType: "json",
					success: () => {
						$.growl.notice({
							title:"متشکریم",
							message:"توضیح شما بعد از تایید ما به نمایش در خواهد آمد."
						});
						$popover.parent().popover("hide").fadeOut();
					},
					error: function(error: any) {
						if(error.error == "data_duplicate" || error.error == "data_validation"){
							let $input = $(`[name="${error.input}"]`, $popover);
							let $params = {
								title: "خطا",
								message:""
							};
							if (error.error == "data_validation") {
								$params.message = "داده وارد شده معتبر نیست";
							}
							if (error.error == "data_duplicate") {
								$params.message = "داده وارد شده تکراری میباشد";
							}
							if ($input.length) {
								$input.inputMsg($params);
							} else {
								$.growl.error($params);
							}
						} else {
							$.growl.error({
								title:"خطا",
								message:"درخواست شما توسط سرور قبول نشد"
							});
						}
						$btn.prop("disabled", false);
						$("i", $btn).removeClass("fa-spinner fa-pulse fa-fw").addClass("fa-check-square-o");
					}
				});
			});
		});
	}
	protected static setDescriptionEvetns($popover: JQuery) {
		$(".tooltips", $popover).tooltip();
		$(".btn-like", $popover).on("click", function(e) {
			e.preventDefault();
			$(this).prop("disabled", true);
			$("i", this).removeClass("thumbs-o-up").addClass("fa-spinner fa-pulse fa-fw");
			const that = this;
			const description = parseInt($(this).data("description"), 10);
			AjaxRequest({
				url: `songs/lyric/descriptions/${description}/like?ajax=1`,
				dataType: "json",
				method: "POST",
				success: function(data) {
					$.growl.notice({
						title: "موفق",
						message: ""
					});
					$(that).prop("disabled", false);
					$("i", that).removeClass("fa-spinner fa-pulse fa-fw").addClass("thumbs-o-up");
					$("span", that).html(data.likes);
					if (data.isLiked) {
						$(that).removeClass("btn-default").addClass("btn-success");
					} else {
						$(that).removeClass("btn-success").addClass("btn-default");
					}
				},
				error: function() {
					$.growl.error({
						title:"خطا",
						message:"درخواست شما توسط سرور قبول نشد"
					});
					$(that).prop("disabled", false);
					$("i", that).removeClass("fa-spinner fa-pulse fa-fw").addClass("thumbs-o-up");
				}
			});
		});
	}
	protected static setCommentsReplyEvents() {
		const $container = $(".lyric .comments-section");
		const $replyContainer = $(".lyric .reply-section");
		$(".comment .btn-reply", $container).on("click", function(e) {
			e.preventDefault();
			const comment = $(this).data("comment");
			$(".comment-reply-sender-name", $replyContainer).html($(this).data("sender-name"));
			$replyContainer.slideDown();
			$("input[name=reply]", $replyContainer).val(comment);
			$("html, body").animate({ scrollTop: $(".panel-comments", $container).offset().top }, 700);
		});
		$(".bnt-cancel-reply", $replyContainer).on("click", (e) => {
			e.preventDefault();
			$replyContainer.slideUp();
			$("input[name=reply]", $replyContainer).val("");
		});
	}
}