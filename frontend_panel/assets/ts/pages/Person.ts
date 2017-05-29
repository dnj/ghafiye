import * as $ from "jquery";
import "jquery.growl";
import "x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js";
import { Router } from "webuilder";
import AutoComplete from "../classes/AutoComplete";
export default class Person{
	private static $form:JQuery;
	private static runPersonImage(){
		$(".person-image", Person.$form).mouseover(function(){
			$(this).find(".person-image-buttons").css("display", "block");
		});
		$(".person-image", Person.$form).mouseout(function(){
			$(this).find(".person-image-buttons").css("display", "none");
		});
	}
	private static setNamesEvents(container?:JQuery){
		if(typeof container == 'undefined'){
			container = Person.$form;
		}
		$(".name-del", container).on('click', function(e){
			e.preventDefault();
			if($(".langs tr", Person.$form).length > 1){
				$(this).parents("tr").remove();
			}else{
				$.growl.error({title:"خطا!", message:"باید حداقل یک نام وجود داشته باشد!"});
			}
		});
		$.fn.editable.defaults.mode = "inline";
		$('.name', container).editable();
		$('.name', container).on('save', function(e, params) {
			let lang:string = $(this).data('lang');
			$(this).parents("tr").find(`input[name='names[${lang}]']`).val(params.newValue);
		})
	}
	private static setPersonsEvents(container?:JQuery){
		if(typeof container == 'undefined'){
			container = Person.$form;
		}
		$(".person-del", container).on('click', function(e){
			e.preventDefault();
			$(this).parents("tr").remove();
		});
	}
	private static createFieldTranslatedLang(){
		$("#addnameform").submit(function(e){
			e.preventDefault();
			let lang:string = $("#selectLang option:selected", this).val();
			let name:string = $("input[name=name]", this).val();
			let hasLang = false;
			let hasname = false;
			let lang_name:string;
			let langs = $("tbody", Person.$form).data("langs");
			$(".names tr", Person.$form).each(function(){
				if($(this).data("lang") == lang){
					hasLang = true;
					return false;
				}
			});
			$(".name", Person.$form).each(function(){
				if($(this).text().trim().toLowerCase() == name.toLowerCase()){
					hasname = true;
					return false;
				}
			});
			for(let langObj of langs){
				if(langObj.value == lang){
					lang_name = langObj.name;
					break;
				}
			}
			if(hasLang || hasname){
				$.growl.error({title:"خطا!", message:"زبان و یا عنوانی با این مشخصات وجود دارد!"});
			}
			if(!lang.length || !name.length){
				$.growl.error({title:"خطا!", message:"داده وارد شده معتبر نیست!"});
			}
			if(lang.length && name.length && lang_name.length && !hasLang && !hasname){
				let html = `<tr data-lang="${lang}">
					<td class="column-left"><input value="${name}" name="names[${lang}]" type="hidden"> ${lang_name}</td>
					<td class="column-right"><a href="#" data-lang="${lang}" data-type="text" data-pk="1" data-original-title="${name}" class="editable editable-click name" style="display: inline;">${name}</a></td>
					<td class="center"><a href="#" class="btn btn-xs btn-bricky tooltips name-del" title="" data-original-title="حذف"><i class="fa fa-times"></i></a></td>
				</tr>`;
				let $row = $(html).appendTo($('.names', Person.$form));
				Person.setNamesEvents($row);
				$("#addName").modal('hide');
				this.reset();
			}
		});
	}
	public static init(){
		Person.$form = $('.person_edit_form');
		Person.setPersonsEvents();
		Person.runPersonImage();
		Person.createFieldTranslatedLang();
	}
	public static initIfNeeded(){
		if($('body').hasClass('person_edit')){
			Person.init();
		}
	}
}