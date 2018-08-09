import * as $ from "jquery";
import "webuilder";
import "bootstrap";
import { AvatarPreview } from "bootstrap-avatar-preview/AvatarPreview";

export default class Edit {
	public static initIfNeeded() {
		if ($("body").hasClass("contribute-edit")) {
			Edit.init();
		}
	}
	protected static $form: JQuery;
	protected static init() {
		Edit.$form = $(".contribute-edit .panel-edit form");
		Edit.runAvatarPreview();
		Edit.setLyricsEvents($(".edit-panel", Edit.$form));
		Edit.runFormSubmitListener();
		Edit.rememberingFileds();
	}
	protected static runAvatarPreview() {
		new AvatarPreview($(".user-image", Edit.$form));
	}
	protected static setLyricsEvents($container: JQuery) {
		Edit.controlBtns();
		Edit.up($container);
		Edit.down($container);
		Edit.delete($container);
		Edit.add($container);
		$(".edit-input", $container).on("change keyup", function() {
			const $prent = $(this).parents(".has-error");
			if ($prent.length) {
				$prent.removeClass("has-error");
				$(".help-block", $prent).remove();
			}
		});
		$(".tooltips", $container).tooltip();
	}
	protected static controlBtns() {
		$(".edit-panel .btn-up", Edit.$form).prop("disabled", false);
		$(".edit-panel .btn-down", Edit.$form).prop("disabled", false);
		const $first = $(".edit-panel", Edit.$form).first();
		const $last = $(".edit-panel", Edit.$form).last();
		$(".btn-up", $first).prop("disabled", true);
		$(".btn-down", $last).prop("disabled", true);
	}
	protected static shiftIndex() {
		$(".edit-panel .edit-input", Edit.$form).each(function(i) {
			const $panel = $(this).parents(".edit-panel");
			$(this).attr("name", `lyrics[${i}][text]`);
			$(".lyric-id", $panel).attr("name", `lyrics[${i}][id]`);
		})
	}
	protected static up($container: JQuery) {
		$(".btn-up", $container).on("click", function() {
			const $panel = $(this).parents(".edit-panel");
			const $prev = $panel.prev();
			const $this = $(".edit-input", $panel);
			const $thisID = $(".lyric-id", $panel);
			if ($prev.length && $this.val()) {
				const $other = $(".edit-input", $prev);
				const prevValue = $other.val();
				$other.val($this.val());
				$this.val(prevValue);
				$other.focus();
				const $otherID = $(".lyric-id", $prev);
				const otherID = $otherID.val();
				$otherID.val($thisID.val());
				$thisID.val(otherID);

				Edit.controlBtns();
			}
		});
	}
	protected static down($container: JQuery) {
		$(".btn-down", $container).on("click", function() {
			const $panel = $(this).parents(".edit-panel");
			const $next = $panel.next();
			const $this = $(".edit-input", $panel);
			const $thisID = $(".lyric-id", $panel);
			if ($next.length && $this.val()) {
				const $other = $(".edit-input", $next);
				const nextValue = $other.val();
				$other.val($this.val());
				$this.val(nextValue);
				$other.focus();
				const $otherID = $(".lyric-id", $next);
				const otherID = $otherID.val();
				$otherID.val($thisID.val());
				$thisID.val(otherID);

				Edit.controlBtns();
			}
		});
	}
	protected static delete($container: JQuery) {
		$(".btn-delete", $container).on("click", function() {
			const $panel = $(this).parents(".edit-panel");
			$panel.fadeOut(500, () => {
				$panel.remove();
				Edit.shiftIndex();
			});
			Edit.controlBtns();
		});
	}
	protected static add($container: JQuery) {
		$(".btn-add", $container).on("click", function() {
			const $panel = $(this).parents(".edit-panel");
			if (!$(".edit-input", $panel).val()) {
				$(".edit-input", $panel).inputMsg({
					message: "متن عبارت خالی است. ابتدا از این فیلد استفاده کنید"
				});
				return false;
			}
			const panel = `<div class="panel edit-panel">
			<div class="panel-body">
				<div class="input-group">
					<input value="" name="" class="lyric-id" type="hidden">
					<div class="form-group"><input value="" name="" class="form-control edit-input" type="text"></div>
					<div class="input-group-btn">
						<button type="button" class="btn btn-default btn-add tooltips" title="افزودن">
							<i class="fa fa-plus"></i>
						</button>
						<button type="button" class="btn btn-default btn-up tooltips" title="انتقال به بالا">
							<i class="fa fa-arrow-up"></i>
						</button>
						<button type="button" class="btn btn-default btn-down tooltips" title="انتقال به پایین">
							<i class="fa fa-arrow-down"></i>
						</button>
						<button type="button" class="btn btn-default btn-delete tooltips" title="حذف">
							<i class="fa fa-trash"></i>
						</button>
					</div>
				</div>
			</div>
		</div>`;
			const $newPanel = $(panel).insertAfter($panel);
			Edit.setLyricsEvents($newPanel);
			$(".edit-input", $newPanel).focus();
			Edit.shiftIndex();
		});
	}
	protected static runFormSubmitListener() {
		Edit.$form.on("submit", function(e)  {
			e.preventDefault();
			const form = this as HTMLFormElement;
			const reset = () => {
				$(".has-error", this).removeClass("has-error");
				$(".help-block", this).remove();
			};
			if (!Edit.checkForContribute()) {
				$.growl.warning({
					title: "ناموفق",
					message: "برای دریافت امتیاز مشارکت نیاز هست تا فعالیتی داشته باشید"
				});
				return false;
			}
			$(this).formAjax({
				data: new FormData(form),
				contentType: false,
				processData: false,
				success: (data: any) => {
					if (data.hasOwnProperty("contribute")) {
						if (!data.contribute) {
							$.growl.warning({
								title: "ناموفق",
								message: "برای دریافت امتیاز مشارکت نیاز هست تا فعالیتی داشته باشید"
							});
							return;
						}
					}
					$.growl.notice({
						title: "موفق",
						message: "امتیاز فعالیت شما بعد از تایید ترجمه برایتان حساب خواهد شد"
					});
					Edit.rememberingFileds();
				},
				error: function(error: any) {
					reset();
					if (error.error == "data_duplicate" || error.error == "data_validation") {
						if (error.input === "person" || error.input === "group" || error.input === "album") {
							error.input += "_name";
						}
						let $input = $(`[name="${error.input}"]`, form);
						let $params = {
							title: "خطا",
							message: ""
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
							title: "خطا",
							message: "درخواست شما توسط سرور قبول نشد"
						});
					}
				}
			});
		});
	}
	protected static rememberingFileds() {
		$(".edit-panel .edit-input", Edit.$form).each(function() {
			$(this).data("val", $(this).val());
		});
		const $title = $("input[name=title]", Edit.$form);
		$title.data("val", $title.val());
	}
	protected static checkForContribute(): boolean {
		for (const input of $(".edit-panel .edit-input", Edit.$form).get()) {
			if ($(input).val() !== $(input).data("val")) {
				return true;
			}
		}
		const $title = $("input[name=title]", Edit.$form);
		return $title.data("val") !== $title.val();
	}
}
