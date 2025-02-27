import * as $ from "jquery";
import "jquery.growl";
import "x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js";
import { Router, webuilder } from "webuilder";
import AutoComplete from "../classes/AutoComplete";
import "bootstrap-inputmsg";
export default class Genre{
	private static $form:JQuery;
	private static runSongAutoComplete(){
		let ac = new AutoComplete("#GenreSearch input[name=song_name]");
		ac.songs();
	}
	private static setTitlesEvents(container?:JQuery){
		if(typeof container == 'undefined'){
			container = Genre.$form;
		}
		$(".title-del", container).on('click', function(e){
			e.preventDefault();
			if($(".langs tr", Genre.$form).length > 1){
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
	private static createFieldTranslatedLang(){
		$("#addTitleform").on('submit', function(e){
			e.preventDefault();
			let lang:string = $("#selectLang option:selected", this).val() as string;
			let title:string = $("input[name=title]", this).val() as string;
			let hasLang = false;
			let hastitle = false;
			let lang_title:string;
			let langs = $("tbody", Genre.$form).data("langs");
			$(".titles tr", Genre.$form).each(function(){
				if($(this).data("lang") == lang){
					hasLang = true;
					return false;
				}
			});
			$(".title", Genre.$form).each(function(){
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
				let $row = $(html).appendTo($('.table-titles > tbody', Genre.$form));
				Genre.setTitlesEvents($row);
				$("#addTitle").modal('hide');
				this.reset();
			}
		});
	}
	private static runSubmitFormListener(){
		Genre.$form.on('submit', function(e){
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
					}
				}
			});
		});
	}
	public static init(){
		let $body = $('body');
		if($body.hasClass('genre_edit') || $body.hasClass('genre_add')){
			if($body.hasClass('genre_add')){
				Genre.$form = $('.genre_add_form');
			}else{
				Genre.$form = $('.genre_edit_form');
			}
			Genre.setTitlesEvents();
			Genre.createFieldTranslatedLang();
			Genre.runSubmitFormListener();
		}else if($body.hasClass('genre_list')){
			Genre.runSongAutoComplete();
		}
	}
	public static initIfNeeded(){
		if($('body').hasClass('genre_edit') || $('body').hasClass('genre_add') || $('body').hasClass('genre_list')){
			Genre.init();
		}
	}
}