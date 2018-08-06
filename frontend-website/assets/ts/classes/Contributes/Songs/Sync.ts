import * as $ from "jquery";

export default class Sync {
	public static initIfNeeded() {
		if ($("body").hasClass("contribute-sync")) {
			Sync.init();
		}
	}
	protected static $form: JQuery;
	protected static init() {
		Sync.$form = $("body.contribute .panel-sync .panel-body form");
		Sync.setTimePickerEvents();
		Sync.runFormSubmitListener();
	}
	protected static formatTime(time: number): string {
		let min = Math.floor(time / 60);
		let sec = time % 60;
		return (min < 10 ? "0" : "") + min + ":" + (sec < 10 ? "0" : "") + sec
	}
	protected static makeTime(strTime:string): number {
		let $val = strTime.split(":");
		if ($val.length != 2) return -1;
		let $min = parseInt($val[0]);
		let $sec = parseInt($val[1]);
		if (isNaN($min) || isNaN($sec)) {
			return -1;
		}
		return $min * 60 + $sec;
	}
	protected static setTimePickerEvents() {
		$(".sync-input", Sync.$form).on("keydown keyup", function(event) {
			if (event.keyCode == 38 || event.keyCode == 40) {
				if (event.type == "keydown") {
					$(this).data("startloop", true);
					runLooping($(this), event);
				} else if (event.type == "keyup") {
					$(this).data("startloop", false);
				}
			}
		});
		const runLooping = ($element:JQuery, event:JQueryEventObject) => {
			const loop = () => {
				let val = Sync.makeTime($element.val());
				switch(event.keyCode) {
					case(38):
						val++;
						break;
					case(40):
						if (val > 0) val--;
						break;
				}
				$element.val(Sync.formatTime(val));
			}
			let interval = setInterval(function() {
				if ($element.data("startloop")) {
					loop();
				} else {
					clearInterval(interval);
				}
			}, 100);
			loop();
		}
	}
	protected static runFormSubmitListener() {
		Sync.$form.on("submit", function(e) {
			e.preventDefault();
			const form = this as HTMLFormElement;
			const reset = () => {
				$(".has-error", this).removeClass("has-error");
				$(".help-block", this).remove();
			};
			$(this).formAjax({
				success: (data: any) => {
					if (data.hasOwnProperty("contribute")) {
						if (!data.contribute) {
							$.growl.warning({
								title: "توجه",
								message: "برای مشارکت میبایست فعالیتی انجام دهید ."
							});
							return;
						}
					}
					$.growl.notice({
						title: "موفق",
						message: "امتیاز فعالیت شما بعد از تایید ترجمه برایتان حساب خواهد شد"
					});
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
}
