import * as $ from "jquery";
import "jquery.growl";
import "bootstrap";
import "x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js";
import { Router, webuilder } from "webuilder";
import AutoComplete from "../classes/AutoComplete";
import {AvatarPreview} from 'bootstrap-avatar-preview/AvatarPreview';
import viewError from '../classes/viewError';
import {Mian} from '../Main';
export default class Album{
	private static $form:JQuery;
	private static runPersonListener(){
		let ac = new AutoComplete("#addSongForm input[name=person_name]");
		ac.persons();
	}
	private static runSongAutoComplete(){
		let ac = new AutoComplete("#addSongForm input[name=song_name]");
		ac.songs();
	}
	private static setTitlesEvents(container?:JQuery){
		if(typeof container == 'undefined'){
			container = Album.$form;
		}
		$(".title-del", container).on('click', function(e){
			e.preventDefault();
			if($(".langs tr", Album.$form).length > 1){
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
	private static setSongsEvents(container?:JQuery){
		if(!container){
			container = Album.$form;
		}
		$(".song-del", container).on('click', function(e){
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
			let langs = $("tbody", Album.$form).data("langs");
			$(".titles tr", Album.$form).each(function(){
				if($(this).data("lang") == lang){
					hasLang = true;
					return false;
				}
			});
			$(".title", Album.$form).each(function(){
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
				let $row = $(html).appendTo($('.titles', Album.$form));
				Album.setTitlesEvents($row);
				$("#addTitle").modal('hide');
				this.reset();
			}
		});
	}
	private static createFieldSongs(){
		$("#addSongForm").submit(function(e){
			e.preventDefault();
			let song:string = $("input[name='song']", this).val() as string;
			let song_name:string = $("input[name='song_name']", this).val() as string;
			let hassong = false;
			if($(`.songs tr[data-song='${song}']`, Album.$form).length){
				hassong = true;
				$.growl.error({title:"خطا!", message:"آهنگ قبلا به آلبوم اضافه شده است!"});
			}
			if(song.length && song_name.length && !hassong){
				let html = `<tr data-song="${song}">
					<td class="column-left"><input value="${song}" name="songs[]" type="hidden"> <a href="${Router.url('userpanel/songs/edit/'+song)}">${song_name}</a></td>
					<td class="center"><a href="#" class="btn btn-xs btn-bricky tooltips song-del" title="" data-original-title="حذف"><i class="fa fa-times"></i></a></td>
				</tr>`;
				let $row = $(html).appendTo($('.songs', Album.$form));
				Album.setSongsEvents($row);
				$("#addSong").modal('hide');
				this.reset();
			}
		});
	}
	private static selectLangValidate(){
		$("select[name='album-lang']").change(function(){
			let selected:string = $("option:selected", this).val() as string;
			let lang:string = $("option:selected", this).text()
			if(!$(`.titles tr[data-lang=${lang}]`, Album.$form).length){
				$.growl.error({title:"خطا!", message:"باید حتما ترجمه ای با زبان "+lang+" وجود داشته باشد!"});
			}
		});
	}
	private static runAvatarPreview(){
		new AvatarPreview($('.user-image', Album.$form));
	}
	private static runSubmitFormListener(){
		Album.$form.on('submit', function(e){
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
						const view = new Mian();
						view.addError($viewError);
						view.getErrorHTML();
					}
				}
			});
		});
	}
	public static init(){
		let $body = $('body');
		if($body.hasClass('album_edit') || $body.hasClass('album_add')){
			if($body.hasClass('album_edit')){
				Album.$form = $('.album_edit_form');
			}else{
				Album.$form = $('.album_add_form');
			}
			Album.runSongAutoComplete();
			Album.setSongsEvents();
			Album.setTitlesEvents();
			Album.createFieldTranslatedLang();
			Album.createFieldSongs();
			Album.selectLangValidate();
			Album.runAvatarPreview();
			Album.runSubmitFormListener();
		}else if($body.hasClass('album_list')){
			Album.runSongAutoComplete();
		}
	}
	public static initIfNeeded(){
		if($('body').hasClass('album_edit') || $('body').hasClass('album_add') || $('body').hasClass('album_list')){
			Album.init();
		}
	}
}