import * as $ from "jquery";
import "jquery.growl";
import "bootstrap";
import "jquery-bootstrap-checkbox";
import "x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js";
import { Router, AjaxRequest, webuilder } from "webuilder";
import AutoComplete from "../classes/AutoComplete";
export default class Song{
	private static $form:JQuery;
	private static $lyricFields:JQuery;
	private static langs:any;
	private static runSongImage(){
		$(".song-image", Song.$form).mouseover(function(){
			$(this).find(".song-image-buttons").css("display", "block");
		});
		$(".song-image", Song.$form).mouseout(function(){
			$(this).find(".song-image-buttons").css("display", "none");
		});
	}
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
				<div class="col-xs-3">
					<div class="form-group">
						<input value="${Song.formatTime(time)}" name="lyric[][time]" class="form-control lyric_time ltr" type="text">
					</div>
				</div>
				<div class="col-xs-8">
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
				let val:string = $(this).val();
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
			let person:string = $("input[name=person]", this).val();
			let person_name:string = $("input[name=person_name]", this).val();
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
								<label><input type="checkbox" name="persons[${person}][primary]" value="1" class="grey person-primary"></label>
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
			let lang = $('option:selected', this).val();
			let ltr = Song.is_ltr(lang);
			if(ltr){
				$(".lyric_text", Song.$lyricFields).addClass("ltr");
				$("input[name=title]").addClass("ltr");
			}else{
				$(".lyric_text", Song.$lyricFields).removeClass("ltr");
				$("input[name=title]").removeClass("ltr");
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
					<a href="#" class="btn btn-xs btn-bricky tooltips lang-del" title="" data-original-title="حذف"><i class="fa fa-times"></i></a>
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
			parent?:Lyric;
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
					let ltr = Song.is_ltr(lang) ? "ltr" : "";
					let html = `<input class="lyrics" type="hidden" name="lyric_lang" value="${lang}"/>`;
					for(let i=0;i < song.orginalLyric.length;i++){
						let isset = lyrics.length > i;
						html += `<div class="row lyrics" data-lyriclang="${lang}"><div class="col-xs-3"><input value="${(isset && lyrics[i].id) ? lyrics[i].id : ""}" name="lyric[${i}][id]" class="form-control lyric_id" type="hidden">`;
						let $oldRow = getRowByID(song.orginalLyric[i].id);
						if(song.orginalLang){
							html += `<div class="form-group">
								<input value="${isset ? lyrics[i].time : ""}" name="lyric[${i}][time]" class="form-control lyric_time ltr" type="text">
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
							html += `<div class="form-group"><input value="${lyrics[i].text}" name="lyric[${i}][text]" class="form-control lyric_text ${ltr}" type="text"></div></div></div>`;
						}else{
							let $formGroup = $('.form-group', $oldRow).eq(1);
							let className = $formGroup.attr('class');
							let ltrOrginal = Song.is_ltr(song.lang) ? "ltr" : "";
							html += `<div class="${className}">
								<input value="${song.orginalLyric[i].text}" name="" disabled="" class="form-control ${ltrOrginal}" type="text">
								<input value="${song.orginalLyric[i].id}" name="lyric[${i}][parent]" class="form-control" type="hidden">
								<input value="${isset ? lyrics[i].text : ""}" name="lyric[${i}][text]" class="form-control lyric_text ${ltr}" type="text">`;
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
	public static init(){
		let $body = $('body');
		if($body.hasClass('song_add')){
			
			Song.$form = $('.song_add_form');
			Song.$lyricFields = $(".lyricFields", Song.$form)
			Song.setEvents(Song.$form);
			Song.runSongImage();
			Song.runAlbumAutoComplete();
			Song.runGroupAutoComplete();
			Song.runPersonAutoComplete($('#addPersonForm'));
			Song.inputDirection();
			Song.ValidateLyrics();
			Song.createFieldPerson();
			Song.setPersonsEvents();
		}else if($body.hasClass('song_edit')){
			Song.$form = $('.song_edit_form');
			Song.$lyricFields = $(".lyricFields", Song.$form)
			Song.langs = $("tbody.langs", Song.$form).data("langs");
			Song.setEvents(Song.$form);
			Song.runAlbumAutoComplete();
			Song.runGroupAutoComplete();
			Song.runPersonAutoComplete($('#addPersonForm'));
			Song.runSongImage();
			Song.setTitlesEvents();
			Song.ValidateLyrics();
			Song.createFieldTranslatedLang();
			Song.changeLyricLang();
			Song.runLyricLangListerner();
		}else if($body.hasClass('song_list')){
			Song.$form = $('#songsLists');
			Song.runAlbumAutoComplete();
			Song.runGroupAutoComplete();
			Song.runPersonAutoComplete(Song.$form);
		}
	}
	public static initIfNeeded(){
		let $body = $('body');
		if($body.hasClass('song_edit') || $body.hasClass('song_add') || $body.hasClass('song_list')){
			Song.init();
		}
	}
}