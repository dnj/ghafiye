import * as $ from "jquery";
import Contributes from "../../Contributes";
import AutoComplete from "../../AutoComplete";
import { AvatarPreview } from "bootstrap-avatar-preview/AvatarPreview";
import "webuilder";

export default class Add {
	public static initIfNeeded() {
		if (Add.$body.length) {
			Add.init();
		}
	}
	protected static $body = $("body.contribute.contribute-add-song");
	protected static $form: JQuery;
	protected static init() {
		Add.$form = $(".panel-add-song .panel-body form", Add.$body);
		Add.changeLangListener();
		Add.runSingerAutoComplete();
		Add.runAlbumAutoComplete();
		Add.runGroupAutoComplete();
		Add.runAvatarPreview();
		Add.runFormSubmitListener();
	}
	protected static changeLangListener() {
		$("select[name=lang]", Add.$form).on("change", function() {
			const lang = $(this).val() as string;
			if (Contributes.isLtr(lang)) {
				$("input", Add.$form).addClass("ltr");
				$("textarea", Add.$form).addClass("ltr");
			} else {
				$("input", Add.$form).removeClass("ltr");
				$("textarea", Add.$form).removeClass("ltr");
			}
		});
	}
	protected static runSingerAutoComplete() {
		const $ac = new AutoComplete($("input[name=person_name]", Add.$form));
		$ac.persons();
	}
	protected static runAlbumAutoComplete() {
		const $ac = new AutoComplete($("input[name=album_name]", Add.$form));
		$ac.albums();
	}
	protected static runGroupAutoComplete() {
		const $ac = new AutoComplete($("input[name=group_name]", Add.$form));
		$ac.groups();
	}
	protected static runAvatarPreview() {
		new AvatarPreview($(".user-image", Add.$form));
	}
	protected static runFormSubmitListener() {
		Add.$form.on("submit", function(e)  {
			e.preventDefault();
			const form = this as HTMLFormElement;
			const reset = () => {
				$(".has-error", this).removeClass("has-error");
				$(".help-block", this).remove();
			};
			$(this).formAjax({
				url: "contribute/song/add?ajax=1",
				data: new FormData(this as HTMLFormElement),
				contentType: false,
				processData: false,
				success: (data: any) => {
					$.growl.notice({
						title: "موفق",
						message: "امتیاز فعالیت شما بعد از تایید آهنگ برایتان حساب خواهد شد"
					});
					form.reset();
					reset();
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
}
