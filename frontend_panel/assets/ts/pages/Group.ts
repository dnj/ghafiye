import * as $ from "jquery";
import "jquery-ui";
import "jquery.growl";
import "x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js";
import { Router, webuilder } from "webuilder";
import AutoComplete from "../classes/AutoComplete";
import {AvatarPreview} from 'bootstrap-avatar-preview/AvatarPreview';
import viewError from '../classes/viewError';
import View from '../classes/view';
export default class Group{
	private static $form:JQuery;
	private static runPersonListener(){
		let ac = new AutoComplete("#groupSearch input[name=person_name]");
		ac.persons();
	}
	private static runPersonAutoComplete(){
		let ac = new AutoComplete("#addPersonForm input[name=person_name]");
		ac.persons();
	}
	private static setTitlesEvents(container?:JQuery){
		if(typeof container == 'undefined'){
			container = Group.$form;
		}
		$(".title-del", container).on('click', function(e){
			e.preventDefault();
			if($(".titles tr", Group.$form).length > 1){
				$(this).parents("tr").remove();
			}else{
				$.growl.error({title:"خطا!", message:"باید حداقل یک عنوان وجود داشته باشد!"});
			}
		});
		$.fn.editable.defaults.mode = "inline";
		$('.title', container).editable();
		$('.title', container).on('save', function(e, params) {
			let lang:string = $(this).data('lang');
			$(this).parents("tr").find(`input[name='titles[${lang}]']`).val(params.newValue);
		})
	}
	private static setPersonsEvents(container?:JQuery){
		if(typeof container == 'undefined'){
			container = Group.$form;
		}
		$(".person-del", container).on('click', function(e){
			e.preventDefault();
			$(this).parents("tr").remove();
		});
	}
	private static createFieldTranslatedLang(){
		$("#addTitleform").submit(function(e){
			e.preventDefault();
			let lang:string = $("#selectLang option:selected", this).val() as string;
			let title:string = $("input[name=title]", this).val() as string;
			let hasLang = false;
			let hastitle = false;
			let lang_title:string;
			let langs = $("tbody", Group.$form).data("langs");
			$(".titles tr", Group.$form).each(function(){
				if($(this).data("lang") == lang){
					hasLang = true;
					return false;
				}
			});
			$(".title", Group.$form).each(function(){
				if($(this).text().trim().toLowerCase() == title.toLowerCase()){
					hastitle = true;
					return false;
				}
			});
			for(let langObj of langs){
				if(langObj.value == lang){
					lang_title = langObj.title;
					break;
				}
			}
			if(hasLang || hastitle){
				$.growl.error({title:"خطا!", message:"زبان و یا عنوانی با این مشخصات وجود دارد!"});
			}
			if(!lang.length || !title.length){
				$.growl.error({title:"خطا!", message:"داده وارد شده معتبر نیست!"});
			}
			if(lang.length && title.length && lang_title.length && !hasLang && !hastitle){
				let html = `<tr data-lang="${lang}">
					<td class="column-left"><input value="${title}" name="titles[${lang}]" type="hidden"> ${lang_title}</td>
					<td class="column-right"><a href="#" data-lang="${lang}" data-type="text" data-pk="1" data-original-title="${title}" class="editable editable-click title" style="display: inline;">${title}</a></td>
					<td class="center"><a href="#" class="btn btn-xs btn-bricky tooltips title-del" title="" data-original-title="حذف"><i class="fa fa-times"></i></a></td>
				</tr>`;
				let $row = $(html).appendTo($('.titles', Group.$form));
				Group.setTitlesEvents($row);
				$("#addTitle").modal('hide');
				this.reset();
			}
		});
	}
	private static createFieldPersons(){
		$("#addPersonForm").submit(function(e){
			e.preventDefault();
			let person:string = $("input[name='person']", this).val() as string;
			let person_name:string = $("input[name='person_name']", this).val() as string;
			let hasPerson = false;
			if($(`.persons tr[data-person='${person}']`, Group.$form).length){
				hasPerson = true;
				$.growl.error({title:"خطا!", message:"شخص قبلا به گروه اضافه شده است"});
			}
			if(person.length && person_name.length && !hasPerson){
				let html = `<tr data-person="${person}">
					<td class="column-left"><input value="${person}" name="persons[]" type="hidden"> <a href="${Router.url('userpanel/persons/edit/'+person)}">${person_name}</a></td>
					<td class="center"><a href="#" class="btn btn-xs btn-bricky tooltips person-del" title="" data-original-title="حذف"><i class="fa fa-times"></i></a></td>
				</tr>`;
				let $row = $(html).appendTo($('.persons', Group.$form));
				Group.setPersonsEvents($row);
				$("#addPerson").modal('hide');
				this.reset();
			}
		});
	}
	private static selectLangValidate(){
		$("select[name='group-lang']").change(function(){
			let selected:string = $("option:selected", this).val() as string;
			let lang:string = $("option:selected", this).text()
			if(!$(`.titles tr[data-lang=${lang}]`, Group.$form).length){
				$.growl.error({title:"خطا!", message:"باید حتما ترجمه ای با زبان "+lang+" وجود داشته باشد!"});
			}
		});
	}
	private static runAvatarPreview(){
		new AvatarPreview($('.user-image', Group.$form));
		new AvatarPreview($(".group-cover", Group.$form));
	}
	private static runSubmitFormListener(){
		Group.$form.on('submit', function(e){
			e.preventDefault();
			$(this).formAjax({
				data: new FormData(this as HTMLFormElement),
				contentType: false,
				processData: false,
				success: (data: webuilder.AjaxResponse) => {
					$.growl.notice({
						title:"موفق",
						message:"انجام شد ."
					});
					if(data.redirect){
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
					}else{
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
					}
				}
			});
		});
	}
	public static init(){
		let $body = $('body');
		if($body.hasClass('group_edit') || $body.hasClass('group_add')){
			if($body.hasClass('group_edit')){
				Group.$form = $('.group_edit_form');
			}else{
				Group.$form = $('.group_add_form');
			}
			Group.runSubmitFormListener();
			Group.runPersonAutoComplete();
			Group.setPersonsEvents();
			Group.setTitlesEvents();
			Group.createFieldTranslatedLang();
			Group.createFieldPersons();
			Group.selectLangValidate();
			Group.runAvatarPreview();
		}else if($body.hasClass('group_list')){
			Group.runPersonListener();
		}
	}
	public static initIfNeeded(){
		if($('body').hasClass('group_edit') || $('body').hasClass('group_add') || $('body').hasClass('group_list')){
			Group.init();
		}
	}
}