/// <reference types="x-editable" />

import * as $ from "jquery";
import "bootstrap";
import "jquery.growl";
import { Router } from "webuilder";
import "webuilder/formAjax";
import "x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js";
import AutoComplete from "../classes/AutoComplete";
import { AvatarPreview } from "bootstrap-avatar-preview/AvatarPreview";
import viewError from '../classes/viewError';
import View from '../classes/view';

export default class Person{
	private static $form: JQuery;
	private static runPersonImage(){
		new AvatarPreview($(".person-cover", Person.$form));
		new AvatarPreview($(".person-image", Person.$form));
	}
	private static setNamesEvents(container?: JQuery){
		if (typeof container == "undefined"){
			container = Person.$form;
		}
		$(".name-del", container).on("click", function(e){
			e.preventDefault();
			if ($(".langs tr", Person.$form).length > 1){
				$(this).parents("tr").remove();
			}else{
				$.growl.error({title: "خطا!", message: "باید حداقل یک نام وجود داشته باشد!"});
			}
		});
		$(".name", container).editable({
			clear: false,
			mode: "popup",
		});
		$(".name", container).on("save", function(e, params) {
			const lang: string = $(this).data("lang");
			$(this).parents("tr").find(`input[name='names[${lang}]']`).val(params.newValue);
		});
	}
	private static setPersonsEvents(container?: JQuery){
		if (typeof container == "undefined"){
			container = Person.$form;
		}
		$(".person-del", container).on("click", function(e){
			e.preventDefault();
			$(this).parents("tr").remove();
		});
	}
	private static createFieldTranslatedLang(){
		$("#addnameform").submit(function(e){
			e.preventDefault();
			const selectedLang = $("select[name=lang] option:selected", this);
			const lang: string = selectedLang.val();
			const lang_name = selectedLang.html();
			const name: string = $("input[name=name]", this).val();
			let hasLang = false;
			let hasname = false;
			$(".names tr", Person.$form).each(function(){
				if ($(this).data("lang") == lang){
					hasLang = true;
					return false;
				}
			});
			$(".name", Person.$form).each(function(){
				if ($(this).text().trim().toLowerCase() == name.toLowerCase()){
					hasname = true;
					return false;
				}
			});
			if (hasLang || hasname){
				$.growl.error({title: "خطا!", message: "زبان و یا عنوانی با این مشخصات وجود دارد!"});
			}
			if (!lang.length || !name.length){
				$.growl.error({title: "خطا!", message: "داده وارد شده معتبر نیست!"});
			}
			if (lang.length && name.length && lang_name.length && !hasLang && !hasname){
				const html = `<tr data-lang="${lang}">
					<td class="column-left"><input value="${name}" name="names[${lang}]" type="hidden"> ${lang_name}</td>
					<td class="column-right"><a href="#" data-lang="${lang}" data-type="text" data-pk="1" data-original-title="${name}" class="editable editable-click name" style="display: inline;">${name}</a></td>
					<td class="center"><a href="#" class="btn btn-xs btn-bricky tooltips name-del" title="" data-original-title="حذف"><i class="fa fa-times"></i></a></td>
				</tr>`;
				const $row = $(html).appendTo($(".table-names > tbody", Person.$form));
				Person.setNamesEvents($row);
				$("#addName").modal("hide");
				this.reset();
			}
		});
	}
	private static ajaxForm(){
		Person.$form.on('submit', function(e){
			e.preventDefault();
			$(this).formAjax({
				data:new FormData(this),
				processData: false,
				contentType: false,
				success: (data) => {
					$.growl.notice({title: "موفقیت آمیز!", message: "تغییرات با موفقیت ذخیره شد."});
					if(data.hasOwnProperty('redirect')){
						window.location.href = data.redirect;
					}
				},
				error: function(error:webuilder.AjaxError){
					if(error.error == 'data_duplicate' || error.error == 'data_validation'){
						let $input = $('[name='+error.input+']');
						let $params = {
							title: 'خطا',
							message:''
						};
						if(error.error == 'data_validation'){
							$params.message = 'داده وارد شده معتبر نیست';
						}
						if(error.error == 'data_duplicate'){
							$params.message = 'داده وارد شده تکراری میباشد';
						}
						if($input.length){
							$input.inputMsg($params);
						}else{
							$.growl.error($params);
						}
					}else if(error.hasOwnProperty('message')){
						$.growl.error({
							title:"خطا",
							message:'درخواست شما توسط سرور قبول نشد'
						});
						const $error:any = error;
						const $viewError = new viewError();
						$viewError.setType($error.setType);
						$viewError.setCode($error.code);
						$viewError.setMessage($error.message);
						$viewError.setData($error.data);
						const view = new View();
						view.addError($viewError);
						view.getErrorHTML();
					}else{
						$.growl.error({
							title:"خطا",
							message:'درخواست شما توسط سرور قبول نشد'
						});
					}
				}
			});
		});
	}
	private static runDeleteModal(){
		
	}
	public static init(){
		const $body = $('body');
		if($body.hasClass('person_list')){
			Person.runDeleteModal();
		}else{
			if($body.hasClass('person_add')){
				Person.$form = $(".person_add_form");
			}else if ($body.hasClass("person_edit")){
				Person.$form = $(".person_edit_form");
			}
			Person.setPersonsEvents();
			Person.runPersonImage();
			Person.createFieldTranslatedLang();
			Person.setNamesEvents();
			Person.ajaxForm();
		}
	}
	public static initIfNeeded(){
		const $body = $('body');
		if ($body.hasClass("person_edit") || $body.hasClass('person_add')){
			Person.init();
		}
	}
}
