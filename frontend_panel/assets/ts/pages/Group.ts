import * as $ from "jquery";
import "jquery.growl";
import "x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js";
import { Router } from "webuilder";
import AutoComplete from "../classes/AutoComplete";
export default class Group{
	private static $form:JQuery;
	private static runGroupImage(){
		$(".group-image", Group.$form).mouseover(function(){
			$(this).find(".group-image-buttons").css("display", "block");
		});
		$(".group-image", Group.$form).mouseout(function(){
			$(this).find(".group-image-buttons").css("display", "none");
		});
	}
	private static runPersonListener(){
		let ac = new AutoComplete("#groupSearch input[name=person_name]");
		ac.persons();
	}
	private static runSongAutoComplete(){
		let ac = new AutoComplete("#addSongForm input[name=song_name]");
		ac.songs();
	}
	private static setTitlesEvents(container?:JQuery){
		if(typeof container == 'undefined'){
			container = Group.$form;
		}
		$(".title-del", container).on('click', function(e){
			e.preventDefault();
			if($(".langs tr", Group.$form).length > 1){
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
			let lang:string = $("#selectLang option:selected", this).val();
			let title:string = $("input[name=title]", this).val();
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
			let person:string = $("input[name='person']", this).val();
			let person_name:string = $("input[name='person_name']", this).val();
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
			let selected:string = $("option:selected", this).val();
			let lang:string = $("option:selected", this).text()
			if(!$(`.titles tr[data-lang=${lang}]`, Group.$form).length){
				$.growl.error({title:"خطا!", message:"باید حتما ترجمه ای با زبان "+lang+" وجود داشته باشد!"});
			}
		});
	}
	public static init(){
		let $body = $('body');
		if($body.hasClass('group_edit') || $body.hasClass('group_add')){
			if($body.hasClass('group_edit')){
				Group.$form = $('.group_edit_form');
				Group.runSongAutoComplete();
				Group.setPersonsEvents();
				Group.setTitlesEvents();
				Group.createFieldTranslatedLang();
				Group.createFieldPersons();
				Group.selectLangValidate();
			}else{
				Group.$form = $('.group_add_form');
			}
			Group.runGroupImage();
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