import * as $ from "jquery";

export default class Edit {
	public static initIfNeeded() {
		if ($("body").hasClass("songs-lyrics-edit")) {
			Edit.init();
		}
	}
	protected static init() {
		Edit.runFormSubmitListener();
	}
	protected static runFormSubmitListener() {
		$(".songs-lyrics-edit .panel form").on("submit", function(e) {
			e.preventDefault();
			const form = this as HTMLFormElement;
			($(this) as any).formAjax({
				dataType: "json",
				success: () => {
					$.growl.notice({
						title: "موفق",
						message: "اطلاعات با موفقیت ذخیره شد"
					});
				},
				error: function(error: any) {
					if(error.error == "data_duplicate" || error.error == "data_validation"){
						let $input = $(`[name="${error.input}"]`, form);
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
				}
			});
		});
	}
}
