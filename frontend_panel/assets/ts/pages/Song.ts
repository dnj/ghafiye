import * as $ from "jquery";
import "jquery.growl";
import "bootstrap";
import "jquery-bootstrap-checkbox";
import "x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js";
import { Router, AjaxRequest, webuilder } from "webuilder";
import AutoComplete from "../classes/AutoComplete";
import {AvatarPreview} from 'bootstrap-avatar-preview/AvatarPreview';
import htmlSpecialChars from '../classes/htmlSpecialChars'
import Descriptions from "./Songs/Lyrics/Descriptions";
export default class Song{
	private static $form:JQuery;
	private static $lyricFields:JQuery;
	private static langs:any;
	private static runAlbumAutoComplete(){
		let ac = new AutoComplete($("input[name=album_name]",Song.$form));
		ac.albums();
	}
	private static runGroupAutoComplete(){
		let ac = new AutoComplete($("input[name=group_name]",Song.$form));
		ac.groups();
	}
	private static runPersonAutoComplete($form:JQuery){
		let ac = new AutoComplete($("input[name=person_name]",$form));
		ac.persons();
	}
	private static runSongAutoComplete(){
		let ac = new AutoComplete("#addSongForm input[name=song_name]");
		ac.songs();
	}
	private static setTitlesEvents(container?:JQuery){
		if(typeof container == 'undefined'){
			container = Song.$form;
		}
		$(".title-del", container).on('click', function(e){
			e.preventDefault();
			if($(".langs tr", Song.$form).length > 1){
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
	private static formatTime(time:number):string{
		let min = Math.floor(time / 60);
		let sec = time % 60;
		let formatted:string;
		if(min < 10){
			formatted = "0"+min+":";
		}else{
			formatted = min+":";
		}
		if(sec < 10){
			formatted += "0"+sec;
		}else{
			formatted += sec;
		}
		return formatted;
	}
	private static setEvents($row:JQuery){
		function shiftTime($row:JQuery){
			let  $lyrics = $(".lyrics", Song.$lyricFields);
			let  eq:number;
			let  found = false;
			for(let  i=0;i<$lyrics.length && !found;i++){
				if($lyrics.eq(i).is($row)){
					eq = i;
					found = true;
				}
			}
			if(found){
				let  time = makeTime($lyrics.last().find(".lyric_time").val());
				time += 2;
				for(let  i = eq + 1; i < $lyrics.length ;i++){
					let  tmp = $lyrics.eq(i+1).find(".lyric_time").val();
					$lyrics.eq(i).find(".lyric_time").val(tmp);
				}
				$(".lyrics:last .lyric_time", Song.$lyricFields).val(Song.formatTime(time));
			}
		}
		function shiftIndex($row:JQuery){
			let $lyrics = $(".lyrics", Song.$lyricFields);
			let eq:number;
			let found = false;
			for(let i = 0;i < $lyrics.length && !found;i++){
				if($lyrics.eq(i).is($row)){
					eq = i;
					found = true;
				}
			}
			if(found){
				let name = $row.find(".lyric_text").attr('name');
				let index = parseInt(name.match(/(\d+)/)[0]) + 1;
				for(let i = eq + 1; i < $lyrics.length ;i++, index++){
					$lyrics.eq(i).find(".lyric_id").attr('name', 'lyric['+index+'][id]');
					$lyrics.eq(i).find(".lyric_time").attr('name', 'lyric['+index+'][time]');
					$lyrics.eq(i).find(".lyric_text").attr('name', 'lyric['+index+'][text]');
				}
			}
		}
		function makeTime(strTime:string):number{
			let $val = strTime.split(":");
			if($val.length != 2)return -1;
			let $min = parseInt($val[0]);
			let $sec = parseInt($val[1]);
			if(isNaN($min) || isNaN($sec)){
				return -1;
			}
			return $min * 60 + $sec;
		}
		
		function appendInput($row:JQuery):JQuery{
			let time = makeTime($("input.lyric_time", $row).val()) + 2;
			let ltr = $("input.lyric_text").hasClass("ltr") ? 'ltr' : "";
			let lang:string = $row.data('lyriclang');
			let html = `<div class="row lyrics" data-lyriclang="${lang}">
				<div class="col-sm-3 col-xs-4">
					<div class="form-group">
						<input value="${Song.formatTime(time)}" name="lyric[][time]" class="form-control lyric_time ltr" type="text">
					</div>
				</div>
				<div class="col-sm-9 col-xs-8">
					<div class="form-group">
						<input value="" name="lyric[][text]" class="form-control lyric_text ${ltr}" type="text">
					</div>
				</div>
			</div>`;
			let $newRow = $(html).insertAfter($row);
			Song.setEvents($newRow);
			shiftIndex($row);
			return $newRow;
		}
		function addInputListener(){
			$("input.lyric_text:last", $row).on("keyup", function(e){
				let val:string = $(this).val() as string;
				let ltr = $(this).hasClass("ltr") ? 'ltr' : "";
				let lang:string = $(this).data("lyriclang");
				if(val){
					let isLast = $(this).is($("input.lyric_text:last", Song.$lyricFields));
					if(isLast){
						let $lyricsFields = appendInput($(this).parents('.lyrics'));
						Song.setEvents($lyricsFields);
					}
				}else{
					let $inputs = $(".lyric_text", Song.$lyricFields);
					if($(this).is($inputs.eq($inputs.length - 2))){
						if(!$inputs.last().val()){
							$inputs.last().parents(".lyrics").remove();
						}
					}
				}
			});
		}
		function setTimePickerEvents(){
			$('.lyric_time', $row).on("keydown keyup", function(event){
				if(event.keyCode == 38 || event.keyCode == 40){
					if(event.type == 'keydown'){
						$(this).data('startloop', true);
						runLooping($(this), event);
					}else if(event.type == 'keyup'){
						$(this).data('startloop', false);
					}
				}
			});
			function runLooping($element:JQuery, event:JQueryEventObject){
				function loop(){
					let val = makeTime($element.val());
					switch(event.keyCode){
						case(38):
							val++;
							break;
						case(40):
							if(val > 0)
								val--;
							break;
					}
					$element.val(Song.formatTime(val));
				}
				let interval =setInterval(function(){
					if($element.data('startloop')){
						loop();
					}else{
						clearInterval(interval);
					}
				}, 100);
				loop();
			}
		}
		function moveDownInput($row:JQuery){
			let $nextLyric = $('.lyric_text', $row.next());
			let $thisLyric = $('.lyric_text', $row);
			if(!$nextLyric.length){
				$thisLyric.trigger('keyup');
				$nextLyric = $('.lyric_text', $row.next());
			}
			let tmp = $nextLyric.val();
			$nextLyric.val($thisLyric.val());
			$thisLyric.val(tmp);
			$nextLyric.focus();
			$nextLyric.data('keys', $thisLyric.data('keys'));
			$thisLyric.data('keys', []);
		}
		function moveUpInput($row:JQuery){
			let $beforeLyric = $('.lyric_text', $row.prev());
			if($beforeLyric.length){
				let $thisLyric = $('.lyric_text', $row);
				let tmp = $beforeLyric.val();
				$beforeLyric.val($thisLyric.val());
				$thisLyric.val(tmp);
				$beforeLyric.focus();
				$beforeLyric.data('keys', $thisLyric.data('keys'));
				$thisLyric.data('keys', []);
			}
		}
		function duplicateInput($row:JQuery){
			let $thisLyric = $('.lyric_text', $row);
			let $thisLyricTime = $('.lyric_time', $row);
			let $newRow = appendInput($row);
			$(".lyric_text", $newRow).val($thisLyric.val());
		}
		setTimePickerEvents();
		addInputListener();
		$('input.lyric_text', $row).on('keydown', function(e){
			let shortcuts = [
				{
					keys:[17,40],
					action:"movedown"
				},
				{
					keys:[17,38],
					action:"moveup"
				},
				{
					keys:[17,16,68],
					action:"duplicate"
				},
				{
					keys:[17,16,40],
					action:"appedInput"
				}
			];
			let found = false;
			foundfor: 
			for(let n=0;n<shortcuts.length;n++){
				for(let m=0;m<shortcuts[n].keys.length;m++){
					if(e.keyCode == shortcuts[n].keys[m]){
						found = true;
						break foundfor;
					}
				}
			}
			if(!found){
				return;
			}
			let $this = $(this);
			let keys = $this.data("keys");
			if(!keys){
				keys = [];
			}
			keys.push(e.keyCode);
			$this.data("keys", keys);
			let shortcut = null;
			for(let n=0;n<shortcuts.length;n++){
				if(shortcuts[n].keys.length == keys.length){
					let pressedKeys = 0;
					for(let m=0;m<shortcuts[n].keys.length;m++){
						
						for(let i=0;i<keys.length;i++){
							if(keys[i] == shortcuts[n].keys[m]){
								pressedKeys++;
								break;
							}
						}
					}
					if(pressedKeys == shortcuts[n].keys.length){
						shortcut = shortcuts[n];
						break;
					}
				}
			}
			if(shortcut){
				let $row = $this.parents('.lyrics');
				switch(shortcut.action){
					case('appedInput'):
						appendInput($row);
						shiftTime($row);
						shiftIndex($row);
						break;
					case('movedown'):
						moveDownInput($row);
						break;
					case('moveup'):
						moveUpInput($row);
						break;
					case('duplicate'):
						duplicateInput($row);
						shiftTime($row);
						shiftIndex($row);
						break;
				}
			}
		});
		$('input.lyric_text', $row).on('keyup', function(e){
			let keys = $(this).data("keys");
			if(!keys){
				keys = [];
			}
			let index = keys.indexOf(e.keyCode);
			if(index >= 0){
				let newkeys = [];
				for(let i = 0;i!=keys.length-1;i++){
					if(i != index){
						newkeys.push(keys[i]);
					}
				}
				$(this).data("keys", newkeys);
			}
		});
	}
	private static setPersonsEvents(container?:JQuery){
		if(typeof container == 'undefined'){
			container = Song.$form;
		}
		let $inputs = $('.person-primary', container);
		$inputs.on("change", function(e){
			if($(this).prop('checked')){
				let $tr = $(this).parents('tr');
				let $val = $(".person-role", $tr).val();
				$(".persons .person-role", Song.$form).each(function(){
					let $this = $(this);
					let $itemTr = $this.parents('tr');
					if($this.val() == $val && !$itemTr.is($tr)){
						$('.person-primary', $itemTr).prop('checked', false).trigger('change');
					}
				});
			}
		});
		$(".person-del", container).on('click', function(e){
			e.preventDefault();
			if($(".persons tr", Song.$form).length > 1){
				$(this).parents("tr").remove();
			}else{
				$.growl.error({title:"خطا!", message:"باید حداقل یک شخص وجود داشته باشد!"});
			}
		});
		$(".person-role", container).on("change", function(){
			$(".person-primary", $(this).parents('tr')).prop('checked', false);
		});
	}
	private static createFieldPerson(){
		$("#addPersonForm").submit(function(e){
			e.preventDefault();
			let person:string = $("input[name=person]", this).val() as string;
			let person_name:string = $("input[name=person_name]", this).val() as string;
			let hasPerson = false;
			if($(`.persons tr[data-person=${person}]`, Song.$form).length){
				hasPerson = true;
				$.growl.error({title:"خطا!", message:"شخص قبلا اضافه شده است"});
			}
			if(person.length && person_name.length && !hasPerson){
				let $html = `<tr data-person="${person}">
					<td class="column-left">
						<input value="${person}" name="persons[${person}][id]" class="form-control" type="hidden">
						<a href="${Router.url("userpanel/persons/edit/"+person)}" target="_blank">${person_name}</a>
					</td>
					<td>
						<div class="form-group">
							<select name="persons[${person}][role]" class="form-control person-role">
								<option value="1">خواننده</option>
								<option value="2">نویسنده</option>
								<option value="3">آهنگساز</option>
							</select>
						</div>
					</td>
					<td class="center">
						<div class="form-group">
							<div class="checkbox">
								<label><input type="checkbox" checked="" name="persons[${person}][primary]" value="1" class="grey person-primary"></label>
							</div>
						</div>
					</td>
					<td class="center">
						<a href="#" class="btn btn-xs btn-bricky tooltips person-del" title=" data-original-title="حذف"><i class="fa fa-times"></i></a>
					</td>
				</tr>`;
				let $row = $($html).appendTo($('.persons', Song.$form));
				Song.setPersonsEvents($row);
				$('input[type=checkbox]', $row).bootstrapCheckbox();
				$("#addPerson").modal('hide');
				this.reset();
			}
		});
	}
	private static is_ltr(lang:string):boolean{
		let ltrLangs = ['ar','fa','dv','he','ps','sd','ur','yi','ug','ku'];
		for(let ltrLang of ltrLangs){
			if(ltrLang == lang){
				return false;
			}
		}
		return true;
	}
	private static inputDirection(){
		let langSelector = $("select[name=lang]", Song.$form);
		langSelector.on("change", function(){
			let lang:string = $('option:selected', this).val() as string;
			if(Song.is_ltr(lang)){
				$(".lyric_text", Song.$lyricFields).addClass("ltr");
				$("input[name=title]").addClass("ltr");
				$("textarea[name=lyrics]").addClass("ltr").prop('placeholder', 'Enter lyrics here...');
			}else{
				$(".lyric_text", Song.$lyricFields).removeClass("ltr");
				$("input[name=title]").removeClass("ltr");
				$("textarea[name=lyrics]").removeClass("ltr").prop('placeholder', 'متون را اینجا وارد کنید...');
			}
		});
		langSelector.trigger("change");
	}
	private static ValidateLyrics(){
		Song.$form.on("submit", function(){
			$(".lyric_text", this).each(function(){
				if(!$(this).val()){
					$(this).parents(".lyrics").remove();
				}
			});
		});
	}
	private static createFieldTranslatedLang(){
		$("#addtitleform").on('submit', function(e){
			e.preventDefault();
			let lang:string = $("#selectLang option:selected", this).val();
			let title:string = $("input[name='title']", this).val();
			let hasLang = ($(".langs tr", Song.$form).data("lang") == lang);
			let hasTitle = ($(".title", Song.$form).text().toLowerCase() == title.toLowerCase());
			let lang_title = '';
			for(let i = 0; i < Song.langs.length && !lang_title; i++){
				if(Song.langs[i].value == lang){
					lang_title = Song.langs[i].title;
				}
			}
			if(hasLang || hasTitle){
				$.growl.error({title:"خطا!", message:"زبان و یا نامی با این مشخصات وجود دارد!"});
				return;
			}
			if(!lang.length || !title.length){
				$.growl.error({title:"خطا!", message:"داده وارد شده معتبر نیست!"});
				return;
			}
			if(!lang_title.length){
				$.growl.error({title:"خطا!", message:"زبان انتخاب شده معتبر نیست!"});
				return;
			}
			let html = `<tr data-lang="${lang}">
				<td class="column-left">
					<input value="${title}" name="titles[${lang}]" class="form-control" type="hidden">${lang_title}
				</td>
				<td class="column-right">
					<a href="#" data-lang="${lang}" data-type="text" data-pk="1" data-original-title="${title}" class="editable editable-click title" style="display: inline;">${title}</a>
				</td>
				<td class="center">
					<a href="#" class="btn btn-xs btn-bricky tooltips title-del" title="" data-original-title="حذف"><i class="fa fa-times"></i></a>
				</td>
			</tr>`;
			let $row = $(html).appendTo($('.langs', Song.$form));
			Song.setTitlesEvents($row);
			$("#addtitle").modal('hide');
			this.reset();
		});
	}
	private static changeLyricLang(){
		function getRowByID(id:string|number){
			return $(`input.lyric_id[value="${id}"]`, Song.$form).parents('.lyrics');
		}
		interface LyricChangeAjax extends webuilder.AjaxResponse{
			song:Song;
		}
		interface Song{
			id:number;
			musixmatch_id:string;
			spotify_id:string;
			album:number;
			lyrics?:Lyric[];
			orginalLyric:Lyric[];
			orginalLang:string;
			lang:string;
		}
		interface Lyric{
			id?:number;
			time?:string;
			text:string;
			parent?:Lyric | number;
		}
		let $langForm = $("#changeLyricForm");
		$langForm.on("submit", function(e){
			e.preventDefault();
			let song:number = $(this).data("song");
			let lang:string = $("select[name=lang] option:selected", this).val();
			if($(".lyrics").data("lyriclang") == lang){
				$(this).parents(".modal").modal('hide');
				return;
			}
			let $btn = $("[type=submit]", this);
			$btn.data("html", $btn.html());
			$btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i>');
			AjaxRequest({
				url: 'userpanel/songs/edit/'+song,
				dataType: "json",
				data:{
					langLyric: lang
				},
				success: function( data:LyricChangeAjax) {
					$btn.prop("disabled", false).html($btn.data("html"));
					let song = data.song;
					if(!song.hasOwnProperty('lyrics')){
						song.lyrics = [];
						for(let x=0;x<song.orginalLyric.length;x++){
							song.lyrics.push({
								id:0,
								time:song.orginalLyric[x].time,
								text: "",
								parent: {
									id: song.orginalLyric[x].id,
									time: song.orginalLyric[x].time,
									text: song.orginalLyric[x].text,
								}
							});
						}
					}
					let lyrics = song.lyrics;
					function getTranslateOf(parent:number){
						for(const lyric of lyrics){
							if(lyric.parent == parent){
								return lyric;
							}
						}
						return null;
					}
					let ltr = Song.is_ltr(lang) ? "ltr" : "";
					let html = `<input class="lyrics" type="hidden" name="lyric_lang" value="${lang}"/>`;
					for(let i=0;i < song.orginalLyric.length;i++){
						let $lyric = getTranslateOf(song.orginalLyric[i].id);
						let isset:boolean = true;
						if($lyric === null){
							$lyric = {
								id: 0,
								text: '',
								parent: song.orginalLyric[i]
							};
							isset = false;
						}
						html += `<div class="row lyrics" data-lyriclang="${lang}"><div class="col-xs-3"><input value="${$lyric.id ? $lyric.id : ""}" name="lyric[${i}][id]" class="form-control lyric_id" type="hidden">`;
						let $oldRow = getRowByID(song.orginalLyric[i].id);
						if(song.orginalLang){
							html += `<div class="form-group">
								<input value="${lyrics[i].time}" name="lyric[${i}][time]" class="form-control lyric_time ltr" type="text">
							</div>`;
						}else{
							let $formGroup = $('.form-group', $oldRow).eq(0);
							let className = $formGroup.attr('class');
							html += `<div class="${className}"><input value="${song.orginalLyric[i].time}" name="" disabled="" class="form-control ltr" type="text">`;
							let $help_block = $formGroup.find('.help-block');
							if($help_block.length){
								html += $help_block[0].outerHTML;
							}
							html += '</div>';
						}
						html += '</div><div class="col-xs-8">';
						if(song.orginalLang){
							html += `<div class="form-group"><input value='${htmlSpecialChars(lyrics[i].text, 'ENT_QUOTES')}' name="lyric[${i}][text]" class="form-control lyric_text ${ltr}" type="text"></div></div></div>`;
						}else{
							let $formGroup = $('.form-group', $oldRow).eq(0);
							let ltrOrginal = Song.is_ltr(song.lang) ? "ltr" : "";
							const text = isset ? htmlSpecialChars($lyric.text, 'ENT_QUOTES') : "";
							html += `<div class="form-group">
								<input value='${htmlSpecialChars(song.orginalLyric[i].text)}' name="" readonly="" class="form-control ${ltrOrginal}" type="text">
								<input value="${song.orginalLyric[i].id}" name="lyric[${i}][parent]" class="form-control" type="hidden">
								<input value='${text}' name="lyric[${i}][text]" class="form-control lyric_text ${ltr}" type="text">`;
							let $help_block = $formGroup.find('.help-block');
							if($help_block.length){
								html += $help_block[0].outerHTML;
							}
							html += '</div></div></div>';
						}
					}
					$langForm.parents(".modal").modal('hide');
					$(".lyricFields .lyrics").remove();
					let $lyricFields = $(html).appendTo(Song.$lyricFields);
					if(song.orginalLang){
						Song.setEvents($lyricFields);
					}
					let action = Song.$form.attr('action');
					action = action.replace(/langLyric=[a-z]{2}/i, "langLyric="+lang);
					Song.$form.attr('action', action);

					let importLyrics:string = '';
					$('.lyric_text').each(function(){
						if($(this).val()){
							importLyrics += $(this).val() + '\n';
						}
					});
					$('textarea[name=lyrics]').val(importLyrics);
					if($('.lyric_text').hasClass('ltr')){
						$('textarea[name=lyrics]').addClass('ltr').prop('placeholder', 'Enter lyrics here...');
					}else{
						$('textarea[name=lyrics]').removeClass('ltr').prop('placeholder', 'متون را اینجا وارد کنید...');
					}
					Song.similarLyricAutoComplete();
				},
				error: function(){
					$btn.prop("disabled", false).html($btn.data("html"));
					$.growl.error({title: 'خطا', message: " در حال حاضر سرور پاسخ درخواست شما را به درستی ارسال نمیکند."});
				}
			})
		});
	}
	private static runLyricLangListerner(){
		let lang = location.search.match(/langLyric=([a-z]{2})/i);
		if(!lang)return;
		if(lang[1] != $('input[name=lyric_lang]').val()){
			$("#changeLyricForm select[name=lang]").val(lang[1]);
			$("#changeLyricForm").trigger('submit');
		}
	}
	private static runAvatarPreview(){
		new AvatarPreview($('.user-image', Song.$form));
	}
	private static runSubmitFormListener(){
		Song.$form.on('submit', function(e){
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
					}else if(error.hasOwnProperty('message')){
						const $error = `<div class="alert alert-block alert-danger ">
											<button data-dismiss="alert" class="close" type="button">×</button>
											<h4 class="alert-heading"><i class="fa fa-times-circle"></i> خطا</h4>
											<p>${error.message}</p>
										</div>`;
						$('.container .errors').html($error);
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
	private static importLyrics():void{
		$('#importForm').on('submit', function(e){
			e.preventDefault();
			const lyrics = $('textarea[name=lyrics]').val() as string;
			if(lyrics){
				$('.lyricFields').html('');
				let time = 0;
				let index = 0;
				const lang = $('select[name=lang] option:selected').val() as string;
				for(const lyric of lyrics.split('\n')){
					if(lyric != ''){
						const html = `<div class="row lyrics" data-lyriclang="${$('select[name=lang] option:selected').val()}">
							<div class="col-sm-3 col-xs-4">
								<div class="form-group">
									<input value="${Song.formatTime(++time)}" name="lyric[${index}][time]" class="form-control lyric_time ltr" type="text">
								</div>
							</div>
							<div class="col-sm-9 col-xs-8">
								<div class="form-group">
									<input value="${lyric}" name="lyric[${index}][text]" class="form-control lyric_text ${Song.is_ltr(lang) ? 'ltr' : ''}" type="text">
								</div>
							</div>
						</div>`;
						let $newRow = $(html).appendTo($('.lyricFields'));
						Song.setEvents($newRow);
						$(this).parents(".modal").modal('hide');
						index++;
					}
				}
			}
		});
	}
	private static editImportLyrics():void{
		let lyrics:string = '';
		$('.lyric_text').each(function(){
			lyrics += $(this).val() + '\n';
		});
		$('textarea[name=lyrics]').val(lyrics);

		function makeTime(strTime:string):number{
			let $val = strTime.split(":");
			if($val.length != 2)return -1;
			let $min = parseInt($val[0]);
			let $sec = parseInt($val[1]);
			if(isNaN($min) || isNaN($sec)){
				return -1;
			}
			return $min * 60 + $sec;
		}
		$('#importForm').on('submit', function(e){
			e.preventDefault();
			const lyrics = $('textarea[name=lyrics]').val() as string;
			if(lyrics){
				let time = 0;
				let index = 0;
				const lang = $('select[name=lang] option:selected').val() as string;
				for(const lyric of lyrics.split('\n')){
					if(lyric != ''){
						const input = $(`input[name='lyric[${index}][text]']`);
						if(input.length){
							if(input.val() != lyric){
								$(`input.lyric_text[name='lyric[${index}][text]']`).val(lyric);
							}
						}else if($("input.lyric_time").parents('.lyrics').data('lyriclang') == $('select[name=lang] option:selected').val()){
							const $lastInput = $("input.lyric_time").last();
							let time = makeTime($lastInput.val() as string) + 2;
							let ltr = $("input.lyric_text").hasClass("ltr") ? 'ltr' : "";
							let lang = $lastInput.parents('.lyrics').data('lyriclang');
							const html = `<div class="row lyrics" data-lyriclang="${lang}">
								<div class="col-sm-3 col-xs-4">
									<div class="form-group">
										<input value="${Song.formatTime(time)}" name="lyric[${index}][time]" class="form-control lyric_time ltr" type="text">
									</div>
								</div>
								<div class="col-sm-9 col-xs-8">
									<div class="form-group">
										<input value="${lyric}" name="lyric[${index}][text]" class="form-control lyric_text ${Song.is_ltr(lang) ? 'ltr' : ''}" type="text">
									</div>
								</div>
							</div>`;
							let $newRow = $(html).appendTo($('.lyricFields'));
							Song.setEvents($newRow);
						}
						index++;
						$(this).parents(".modal").modal('hide');
					}
				}
			}
		});
	}
	private static similarLyricAutoComplete(){
		$('.lyric_text', Song.$form).on('change', function(){
			const $row = $(this).parents('.lyrics');
			const lang = $('select[name=lang] option:selected');
			if($row.data('lyriclang') != lang.val()){
				const $changedOriginal = $(this).parents('.form-group').find('input:first');
				const $similars = $('.lyric_text', Song.$form).parents('.form-group').find('input:first[value="'+$changedOriginal.val()+'"]');
				const taht = $(this);
				$similars.each(function(){
					const $parent = $(this).parent();
					if(!$('.lyric_text', $parent).val()){
						$('.lyric_text', $parent).val(taht.val());
					}
				});
				/*$('.lyric_text', Song.$form).each(function(){
					const $thatOriginal = taht.parents('.form-group').find('input:first');
					const $original = $(this).parents('.form-group').find('input:first');
					if(!$thatOriginal.is($original) && $original.val() == $thatOriginal.val()){

						console.log($original);
						if(!$(this).val()){
							$(this).val(taht.val());
						}
					}
				});*/
			}else{
				console.log('salam');
				console.log($row.data('lyriclang'));
				console.log(lang.val());
				console.log($row.data('lyriclang') != lang.val());
			}
		});
	}
	public static init(){
		let $body = $('body');
		if($body.hasClass('song_add')){
			Song.$form = $('.song_add_form');
			Song.$lyricFields = $(".lyricFields", Song.$form)
			Song.setEvents(Song.$form);
			Song.runAvatarPreview();
			Song.runAlbumAutoComplete();
			Song.runGroupAutoComplete();
			Song.runPersonAutoComplete($('#addPersonForm'));
			Song.inputDirection();
			Song.ValidateLyrics();
			Song.createFieldPerson();
			Song.setPersonsEvents();
			Song.runSubmitFormListener();
			Song.importLyrics();
		}else if($body.hasClass('song_edit')){
			Song.$form = $('.song_edit_form');
			Song.$lyricFields = $(".lyricFields", Song.$form)
			Song.langs = $("tbody.langs", Song.$form).data("langs");
			Song.setEvents(Song.$form);
			Song.runAlbumAutoComplete();
			Song.runGroupAutoComplete();
			Song.runPersonAutoComplete($('#addPersonForm'));
			Song.runAvatarPreview();
			Song.setTitlesEvents();
			Song.ValidateLyrics();
			Song.createFieldTranslatedLang();
			Song.changeLyricLang();
			Song.runLyricLangListerner();
			Song.createFieldPerson();
			Song.runSubmitFormListener();
			Song.inputDirection();
			Song.editImportLyrics();
			Song.setPersonsEvents();
		}else if($body.hasClass('song_list')){
			Song.$form = $('#songsLists');
			Song.runAlbumAutoComplete();
			Song.runGroupAutoComplete();
			Song.runPersonAutoComplete(Song.$form);
		}
	}
	public static initIfNeeded(){
		Descriptions.initIfNeeded();
		let $body = $('body');
		if($body.hasClass('song_edit') || $body.hasClass('song_add') || $body.hasClass('song_list')){
			Song.init();
		}
	}
}