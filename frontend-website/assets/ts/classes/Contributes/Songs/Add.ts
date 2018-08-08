import * as $ from "jquery";
import AutoComplete from "../../AutoComplete";
import { AvatarPreview } from "bootstrap-avatar-preview/AvatarPreview";
import "webuilder";
import "bootstrap";
import Contributes from "../../Contributes";

export default class Add {
	public static initIfNeeded() {
		if (Add.$body.length) {
			Add.init();
		}
	}
	protected static $body = $("body.contribute.contribute-add-song");
	protected static $form: JQuery;
	protected static $addSingerform: JQuery;
	protected static $addAlbumForm: JQuery;
	protected static $addGroupForm: JQuery;
	protected static init() {
		Add.$form = $(".panel-add-song .panel-body form", Add.$body);
		Add.$addSingerform = $("#addSingerForm", Add.$body);
		Add.$addGroupForm = $("#addGroupForm", Add.$body);
		Add.$addAlbumForm = $("#addAlbumForm", Add.$body);
		Add.runSingerAutoComplete();
		Add.runAlbumAutoComplete();
		Add.runGroupAutoComplete();
		Add.runAvatarPreview();
		Add.runFormSubmitListener();
		Add.runAddSingerFormListener();
		Add.setAddGroupEvents();
		Add.runAddAlbumFormSubmitListener();
	}
	protected static runSingerAutoComplete() {
		const $ac = new AutoComplete($("input[name=person_name]", Add.$form));
		$ac.persons();
		$("input[name=person_name]", Add.$form).on("change keyup", function() {
			if (!$(this).val()) {
				$("input[name=person]", Add.$form).val("");
			}
		});
	}
	protected static runAlbumAutoComplete() {
		const $ac = new AutoComplete($("input[name=album_name]", Add.$form));
		$ac.albums();
		$("input[name=album_name]", Add.$form).on("change keyup", function() {
			if (!$(this).val()) {
				$("input[name=album]", Add.$form).val("");
			}
		});
	}
	protected static runGroupAutoComplete() {
		const $ac = new AutoComplete($("input[name=group_name]", Add.$form));
		$ac.groups();
		$("input[name=group_name]", Add.$form).on("change keyup", function() {
			if (!$(this).val()) {
				$("input[name=group]", Add.$form).val("");
			}
		});
	}
	protected static runAvatarPreview() {
		new AvatarPreview($(".user-image", Add.$form));
		new AvatarPreview($(".user-image", Add.$addSingerform));
		new AvatarPreview($(".user-image", Add.$addGroupForm));
		new AvatarPreview($(".user-image", Add.$addAlbumForm));
	}
	public static changeLangListener() {
		$(".panel-white select[name=lang]", Add.$form).on("change", function() {
			const lang = $(this).val() as string;
			if (Contributes.isLtr(lang)) {
				$(".panel-white input", Add.$form).addClass("ltr");
				$(".panel-white textarea", Add.$form).addClass("ltr");
			} else {
				$(".panel-white input", Add.$form).removeClass("ltr");
				$(".panel-white textarea", Add.$form).removeClass("ltr");
			}
		});
		$(".panel-white select[name=lang] option:selected", Add.$form).trigger("change");
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
					$("img", Add.$form).attr("src", $(".btn-remove", Add.$form).data("default"));
					reset();
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
	protected static runAddSingerFormListener() {
		Add.$addSingerform.on("submit", function(e) {
			e.preventDefault();
			const form = this as HTMLFormElement;
			const reset = () => {
				$(".has-error", this).removeClass("has-error");
				$(".help-block", this).remove();
			};
			$(this).formAjax({
				url: "contribute/person/add?ajax=1",
				data: new FormData(form),
				contentType: false,
				processData: false,
				success: (data: any) => {
					$.growl.notice({
						title: "موفق",
						message: "امتیاز فعالیت شما بعد از تایید شخص برایتان حساب خواهد شد"
					});
					form.reset();
					reset();
					$("#addSinger").modal("hide");
					$("img", Add.$addSingerform).attr("src", $(".btn-remove", Add.$addSingerform).data("default"));
					$("input[name=person]", Add.$form).val(data.person.id);
					$("input[name=person_name]", Add.$form).val(data.person.name);
				},
				error: function(error: any) {
					reset();
					if (error.error == "data_duplicate" || error.error == "data_validation") {
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
	protected static setAddGroupEvents() {
		const $ac = new AutoComplete($("input[name=person_name]", Add.$addGroupForm));
		$ac.persons();
		const $panel = $(".panel", Add.$addGroupForm);
		const $personName = $("input[name=person_name]", Add.$addGroupForm);
		const $person = $("input[name=person]", Add.$addGroupForm);
		$(".btn-add-person", Add.$addGroupForm).on("click", function(e) {
			e.preventDefault();
			$(".has-error", Add.$addGroupForm).removeClass("has-error");
			$(".help-block", Add.$addGroupForm).remove();
			const person = $person.val();
			const personName = $personName.val();
			if (!person || !personName) {
				$personName.inputMsg({
					message: "شخصی انتخاب کنید",
				});
				return;
			}
			if ($(`input[name="persons[${person}]"]`, $panel).length) {
				$personName.inputMsg({
					message: "شخص قبلا اضافه شده",
				});
				return;
			}
			$panel.show();
			const tr = `<tr>
				<td>
					<input type="hidden" name="persons[${person}]" value="${person}">
					${personName}
				</td>
				<td>
					<button type="button" class="btn btn-xs btn-bricky tooltips btn-remove" title="حذف"><i class="fa fa-times"></i></a>
				</td>
			</tr>`;
			const $tr = $(tr).appendTo($("tbody", $panel));
			$(".btn-remove", $tr).on("click", function(e) {
				$(this).parents("tr").remove();
				if (!$("tbody tr", $panel).length) {
					$panel.hide();
				}
			});
			$(".tooltips", $tr).tooltip();
			$personName.val("");
			$person.val("");
		});
		Add.runAddGroupFormSubmitListener();
	}
	protected static runAddGroupFormSubmitListener() {
		Add.$addGroupForm.on("submit", function(e) {
			e.preventDefault();
			const form = this as HTMLFormElement;
			const reset = () => {
				$(".has-error", this).removeClass("has-error");
				$(".help-block", this).remove();
			};
			$(this).formAjax({
				url: "contribute/group/add?ajax=1",
				data: new FormData(form),
				contentType: false,
				processData: false,
				success: (data: any) => {
					$.growl.notice({
						title: "موفق",
						message: "امتیاز فعالیت شما بعد از تایید گروه اهدا خواهد شد"
					});
					form.reset();
					reset();
					$("#addGroup").modal("hide");
					$("img", Add.$addGroupForm).attr("src", $(".btn-remove", Add.$addGroupForm).data("default"));
					$("input[name=group]", Add.$form).val(data.group.id);
					$("input[name=group_name]", Add.$form).val(data.group.title);
				},
				error: function(error: any) {
					reset();
					if (error.error == "data_duplicate" || error.error == "data_validation") {
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
	protected static runAddAlbumFormSubmitListener() {
		Add.$addAlbumForm.on("submit", function(e) {
			e.preventDefault();
			const form = this as HTMLFormElement;
			const reset = () => {
				$(".has-error", this).removeClass("has-error");
				$(".help-block", this).remove();
			};
			$(this).formAjax({
				url: "contribute/album/add?ajax=1",
				data: new FormData(form),
				contentType: false,
				processData: false,
				success: (data: any) => {
					$.growl.notice({
						title: "موفق",
						message: "امتیاز فعالیت شما بعد از تایید آلبوم اهدا خواهد شد"
					});
					form.reset();
					reset();
					$("#addAlbum").modal("hide");
					$("img", Add.$addAlbumForm).attr("src", $(".btn-remove", Add.$addAlbumForm).data("default"));
					$("input[name=album]", Add.$form).val(data.album.id);
					$("input[name=album_name]", Add.$form).val(data.album.title);
				},
				error: function(error: any) {
					reset();
					if (error.error == "data_duplicate" || error.error == "data_validation") {
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
