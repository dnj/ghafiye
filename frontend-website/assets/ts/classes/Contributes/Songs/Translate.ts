import * as $ from "jquery";
import "webuilder";
import { Router, AjaxRequest } from "webuilder";
import Contributes from "../../Contributes";

interface Lyric {
	parent: number,
	text: string;
}

export default class Translate {
	public static initIfNeeded() {
		if (Translate.$body.length) {
			Translate.init();
		}
	}
	protected static $body = $("body.contribute.contribute-translate");
	protected static $form: JQuery;
	protected static song: number;
	protected static init() {
		Translate.$form = $(".panel-translate .panel-body form", Translate.$body);
		Translate.song = Translate.$form.data("song");
		Translate.changeLangListener();
		Translate.autoTranslateComplete();
		Translate.runFormSubmitListener();
	}
	protected static changeLangListener() {
		const $progress = $(".translate-progress", Translate.$form);
		const lyricsLngth = $(".translate-panel input", Translate.$form).get().length;
		$("select[name=lang]", Translate.$form).on("change", function() {
			const lang = $(this).val() as string;
			if (Contributes.isLtr(lang)) {
				$("input", Translate.$form).addClass("ltr");
			} else {
				$("input", Translate.$form).removeClass("ltr");
			}
			$(this).prop("disabled", false);
			$("> p", $progress).html(`<i class="fa fa-spinner fa-pulse fa-fw"></i> در حال دریافت اطلاعات`);
			const that = this;
			AjaxRequest({
				url: `contribute/song/translate/${Translate.song}`,
				dataType: "json",
				method: "GET",
				data: {
					songlang: lang,
				},
				success: function(data) {
					$("> p", $progress).html(`درصد ترجمه شده: `);
					$(that).prop("disabled", false);
					$(".translate-panel .tanslate-input", Translate.$form).val("");
					for (const lyric of data.items as Lyric[]) {
						$(`input[name="translates[${lyric.parent}]"]`, Translate.$form).val(lyric.text);
					}
					if (data.hasOwnProperty("title")) {
						$("input[name=title]", Translate.$form).val(data.title);
					}
					const percent = (data.items.length * 100) / lyricsLngth;
					$(".progress-bar", $progress).css("width", percent + "%");
					$(".percent", $progress).html(Math.ceil(percent) + "%");
					if (percent > 60) {
						$(".percent", $progress).css("color", "#fff");
					} else {
						$(".percent", $progress).css("color", "#777");
					}
					if (data.hasOwnProperty("errors")) {
						let errors = "";
						for (const error of data.errors) {
							errors += `<div class="alert alert-block alert-warning "><button data-dismiss="alert" class="close" type="button">×</button>
							<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> هشدار</h4>
							<p>${error.message}</p>
						</div>`;
						}
						$(".container .errors").html(errors);
					} else {
						$(".container .errors").html("");
					}
					Translate.rememberingFileds();
				},
				error: function() {
					$("> p", $progress).html(`درصد ترجمه شده: `);
					$.growl.error({
						title: "خطا",
						message: "درخواست شما توسط سرور قبول نشد"
					});
					$(that).prop("disabled", false);
				}
			});
		});
		$("select[name=lang] option:selected", Translate.$form).trigger("change");
	}
	protected static autoTranslateComplete() {
		$("input.tanslate-input", Translate.$form).on("change keyup", function() {
			const val = $(this).val();
			const $lyric = $(this).parents(".translate-panel").find(".lyric");
			const lyric = $lyric.html();
			$(".translate-panel .lyric", Translate.$form).each(function() {
				if (!$(this).is($lyric) && $(this).html() === lyric) {
					$(this).parents(".translate-panel").find(".tanslate-input").val(val);
				}
			});
		});
	}
	protected static runFormSubmitListener() {
		Translate.$form.on("submit", function(e)  {
			e.preventDefault();
			const form = this as HTMLFormElement;
			const reset = () => {
				$(".has-error", this).removeClass("has-error");
				$(".help-block", this).remove();
			};
			if (!Translate.checkForContribute()) {
				$.growl.warning({
					title: "توجه",
					message: "برای دریافت امتیاز مشارکت نیاز هست تا فعالیتی داشته باشید"
				});
				return false;
			}
			$(this).formAjax({
				success: (data: any) => {
					if (data.hasOwnProperty("contribute")) {
						if (!data.contribute) {
							$.growl.warning({
								title: "ناموفق",
								message: "برای دریافت امتیاز مشارکت نیاز است تا فعالیتی داشته باشید ."
							});
							return;
						}
					}
					$.growl.notice({
						title: "موفق",
						message: "امتیاز فعالیت شما بعد از تایید ترجمه اهدا خواهد شد"
					});
					Translate.rememberingFileds();
				},
				error: function(error: any) {
					reset();
					if (error.error == "data_duplicate" || error.error == "data_validation") {
						if (error.input === "person" || error.input === "group" || error.input === "album") {
							error.input += "_name";
						}
						let $input = $(`[name="${error.input}"]`);
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
		$(".translate-panel .tanslate-input", Translate.$form).each(function() {
			$(this).data("val", $(this).val());
		});
		const $title = $("input[name=title]", Translate.$form);
		$title.data("val", $title.val());
	}
	protected static checkForContribute(): boolean {
		for (const input of $(".translate-panel .tanslate-input", Translate.$form).get()) {
			if ($(input).val() !== $(input).data("val")) {
				return true;
			}
		}
		const $title = $("input[name=title]", Translate.$form);
		return $title.data("val") !== $title.val();
	}
}
