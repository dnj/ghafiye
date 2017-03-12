var songEdit = function () {
	var form = $('.create_form');
	var langs = $("tbody", form).data("langs");
	var $lyricFields = $(".lyricFields", form);
	var runAlbumListener = function(){
		$("input[name=album_name]", form).autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: "/fa/userpanel/albums",
					dataType: "json",
					data: {
						ajax:1,
						word: request.term
					},
					success: function( data ) {
						if(data.hasOwnProperty('status')){
							if(data.status){
								if(data.hasOwnProperty('items')){
									response( data.items );
								}
							}
						}

					}
				});
			},
			select: function( event, ui ) {
				$(this).val(ui.item.title);
				$('input[name=album]', form).val(ui.item.id).trigger('change');
				return false;
			},
			focus: function( event, ui ) {
				$(this).val(ui.item.title);
				$('input[name=album]', form).val(ui.item.id);
				return false;
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<strong>" +item.title+ "</strong>" )
				.appendTo( ul );
		};
	};
	var runGroupListener = function(){
		$("input[name=group_name]", form).autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: "/fa/userpanel/groups",
					dataType: "json",
					data: {
						ajax:1,
						word: request.term
					},
					success: function( data ) {
						if(data.hasOwnProperty('status')){
							if(data.status){
								if(data.hasOwnProperty('items')){
									response( data.items );
								}
							}
						}

					}
				});
			},
			select: function( event, ui ) {
				$(this).val(ui.item.title);
				$('input[name=group]', form).val(ui.item.id).trigger('change');
				return false;
			},
			focus: function( event, ui ) {
				$(this).val(ui.item.title);
				$('input[name=group]', form).val(ui.item.id);
				return false;
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<strong>" +item.title+ "</strong>" )
				.appendTo( ul );
		};
	};
	var is_ltr = function($lang){
		$ltrLangs = ['ar','fa','dv','he','ps','sd','ur','yi','ug','ku'];
		for(var i=0;i<10;i++){
			if($ltrLangs[i] == $lang){
				return false;
			}
		}
		return true;
	}
	var mouseHover = function (){
		$(window).ready(function(){
			$(".song-image", form).mouseover(function(){
		        $(this).find(".song-image-buttons").show();
		    });
		    $(".song-image", form).mouseout(function(){
		        $(this).find(".song-image-buttons").hide();
		    });
		});
	}
	var titleDelete = function(e){
		e.preventDefault();
		var $lang_count = 0;
		$(".langs tr", form).each(function(){
			$lang_count++;
		});
		if($lang_count > 1){
			$(this).parents("tr").remove();
		}else{
			$.growl.error({title:"خطا!", message:"باید حداقل یک نام وجود داشته باشد!"});
		}
	}
	var formatTime = function(time){
		var $min = Math.floor(time / 60);
		var $sec = time % 60;
		if($min < 10){
			$min = "0"+$min;
		}
		if($sec < 10){
			$sec = "0"+$sec;
		}
		return $min + ":" + $sec;
	}
	var makeTime = function($strTime){
		var $val = $strTime.split(":");
		if($val.length != 2)return;
		var $min = parseInt($val[0]);
		var $sec = parseInt($val[1]);
		if(isNaN($min) || isNaN($sec)){
			return false;
		}
		return $min * 60 + $sec;
	}
	var appendInput = function($row){
		var time = makeTime($("input.lyric_time", $row).val()) + 2;
		var $ltr = $("input.lyric_text").hasClass("ltr") ? 'ltr' : "";
		var $lang = $row.data('lyriclang');
		var $html = '<div class="row lyrics" data-lyriclang="'+$lang+'"><div class="col-xs-3">';
		$html += '<div class="form-group"><input value="'+formatTime(time)+'" name="lyric[][time]" class="form-control lyric_time ltr" type="text"></div>';
		$html += '</div><div class="col-xs-8">'
		$html += '<div class="form-group"><input value="" name="lyric[][text]" class="form-control lyric_text '+$ltr+'" type="text"></div></div></div>';
		var $newRow = $($html).insertAfter($row);
		setEvents($newRow);
		return $newRow;
	}
	var moveDownInput = function($row){
		var $nextLyric = $('.lyric_text', $row.next());
		var $thisLyric = $('.lyric_text', $row);
		if(!$nextLyric.length){
			$thisLyric.trigger('keyup');
			$nextLyric = $('.lyric_text', $row.next());
		}
		var tmp = $nextLyric.val();
		$nextLyric.val($thisLyric.val());
		$thisLyric.val(tmp);
		$nextLyric.focus();
		$nextLyric.data('keys', $thisLyric.data('keys'));
		$thisLyric.data('keys', []);
	}
	var moveUpInput = function($row){
		var $beforeLyric = $('.lyric_text', $row.prev());
		if($beforeLyric.length){
			var $thisLyric = $('.lyric_text', $row);
			var tmp = $beforeLyric.val();
			$beforeLyric.val($thisLyric.val());
			$thisLyric.val(tmp);
			$beforeLyric.focus();
			$beforeLyric.data('keys', $thisLyric.data('keys'));
			$thisLyric.data('keys', []);
		}
	}
	var duplicateInput = function($row){
		var $thisLyric = $('.lyric_text', $row);
		var $thisLyricTime = $('.lyric_time', $row);
		var $newRow = appendInput($row);
		$(".lyric_text", $newRow).val($thisLyric.val());
	}
	var setEvents = function($row){
		var addInputListener = function(){
			$("input.lyric_text:last", $row).on("keyup", function(e){
				var $val = $(this).val();
				var $ltr = $(this).hasClass("ltr") ? 'ltr' : "";
				var $lang = $(this).data("lyriclang");
				if($val){
					var $isLast = $(this).is($("input.lyric_text:last", $lyricFields));
					if($isLast){
						var lyricsFields = appendInput($(this).parents('.lyrics'));
						setEvents(lyricsFields);
					}
				}else{
					var $inputs = $(".lyric_text", lyricsFields);
					if($(this).is($inputs.eq($inputs.length - 2))){
						if(!$inputs.last().val()){
							$inputs.last().parents(".lyrics").remove();
						}
					}
				}
			});
		}
		var setTimePickerEvents = function(){
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
			
			var runLooping = function($element, event){
				var loop = function(){
					var val = makeTime($element.val());
					switch(event.keyCode){
						case(38):
							$val++;
							break;
						case(40):
							if($val > 0)
							$val--;
							break;
					}
					
					$element.val(formatTime($val));
				};
				var $interval =	setInterval(function(){
					if($element.data('startloop')){
						loop();
					}else{
						clearInterval($interval);
					}
				}, 100);
				loop();
			}
		}
		setTimePickerEvents();
		addInputListener();
		$('input.lyric_text', $row).on('keydown', function(e){
			var shortcuts = [
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
			var found = false;
			foundfor: 
			for(var n=0;n<shortcuts.length;n++){
				for(var m=0;m<shortcuts[n].keys.length;m++){
					if(e.keyCode == shortcuts[n].keys[m]){
						found = true;
						break foundfor;
					}
				}
			}
			if(!found){
				return;
			}
			var $this = $(this);
			var keys = $this.data("keys");
			if(!keys){
				keys = [];
			}
			keys.push(e.keyCode);
			$this.data("keys", keys);
			var shortcut = null;
			for(var n=0;n<shortcuts.length;n++){
				if(shortcuts[n].keys.length == keys.length){
					var pressedKeys = 0;
					for(var m=0;m<shortcuts[n].keys.length;m++){
						
						for(var i=0;i<keys.length;i++){
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
				var $row = $this.parents('.lyrics');
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
			var keys = $(this).data("keys");
			if(!keys){
				keys = [];
			}
			var index = keys.indexOf(e.keyCode);
			if(index >= 0){
				var newkeys = [];
				for(var i = 0;i!=keys.length-1;i++){
					if(i != index){
						newkeys.push(keys[i]);
					}
				}
				$(this).data("keys", newkeys);
			}
		});
	}
	var shiftTime = function($row){
		var $lyrics = $(".lyrics", $lyricFields);
		var eq;
		var found = false;
		for(var i=0;i<$lyrics.length && !found;i++){
			if($lyrics.eq(i).is($row)){
				eq = i;
				found = true;
			}
		}
		if(found){
			var time = makeTime($lyrics.last().find(".lyric_time").val());
			time+=2;
			for(var i = eq + 1; i < $lyrics.length ;i++){
				var tmp = $($lyrics[i+1]).find(".lyric_time").val();
				$($lyrics[i]).find(".lyric_time").val(tmp);
			}
			$(".lyrics:last .lyric_time", $lyricFields).val(formatTime(time));
		}
	}
	var shiftIndex = function($row){
		var $lyrics = $(".lyrics", $lyricFields);
		var eq;
		var found = false;
		for(var i=0;i<$lyrics.length && !found;i++){
			if($lyrics.eq(i).is($row)){
				eq = i;
				found = true;
			}
		}
		if(found){
			var name = $row.find(".lyric_id").attr('name');
			var index = parseInt(name.match(/(\d+)/)[0]) + 1;
			for(var i = eq + 1; i < $lyrics.length ;i++, index++){
				$lyrics.eq(i).find(".lyric_id").attr('name', 'lyric['+index+'][id]');
				$lyrics.eq(i).find(".lyric_time").attr('name', 'lyric['+index+'][time]');
				$lyrics.eq(i).find(".lyric_text").attr('name', 'lyric['+index+'][text]');
			}
		}
	}
	var titleEditSave = function(e, params) {
		var $lang = $(this).data('lang');
		$(this).parents("tr").find("input[name='titles["+$lang+"]']").val(params.newValue);
	}
	var setTitlesEvents = function(container){
		if(!container){
			container = form;
		}
		$(".lang-del", container).click(titleDelete);
		$.fn.editable.defaults.mode = "inline";
		$('.title', container).editable();
		$('.title', container).on('save', titleEditSave);
	}
	var createFieldTranslatedLang = function(){
		$("#addtitleform").submit(function(e){
			e.preventDefault();
			var $lang = $("#selectLang option:selected", this).val();
			var $title = $("input[name='title']", this).val();
			var $hasLang = false;
			var $hasTitle = false;
			var $lang_title = '';
			$hasLang = ($(".langs tr", form).data("lang") == $lang);
			$hasTitle = ($(".title", form).text().toLowerCase() == $title.toLowerCase());
			$(langs).each(function(){
				if(this.value == $lang){
					$lang_title = this.title;
				}
			});
			if($hasLang || $hasTitle){
				$.growl.error({title:"خطا!", message:"زبان و یا نامی با این مشخصات وجود دارد!"});
			}
			if(!$lang.length || !$title.length){
				$.growl.error({title:"خطا!", message:"داده وارد شده معتبر نیست!"});
			}
			if($lang.length && $title.length && $lang_title.length && !$hasLang && !$hasTitle){
				var $html = "<tr data-lang=\""+$lang+"\"><td class=\"column-left\"><input value=\""+$title+"\" title=\"titles["+$lang+"]\" class=\"form-control\" type=\"hidden\">"+$lang_title+"</td>";
			    $html += "<td class=\"column-right\"><a href=\"#\" data-lang=\""+$lang+"\" data-type=\"text\" data-pk=\"1\" data-original-title=\""+$title+"\" class=\"editable editable-click title\" style=\"display: inline;\">"+$title+"</a></td>";
			    $html += "<td class=\"center\"><a href=\"#\" class=\"btn btn-xs btn-bricky tooltips lang-del\" title=\"\" data-original-title=\"حذف\"><i class=\"fa fa-times\"></i></a></td></tr>";
				var $row = $($html).appendTo($('.langs', form));
				setTitlesEvents($row);
				$("#addtitle").modal('hide');
				this.reset();
			}
		});
	}
	var getRowByID = function(id){
		return $('input.lyric_id[value="'+id+'"]', form).parents('.lyrics');
	}
	var  changeLyricLang = function(){
		var $langForm = $("#changeLyricForm");
		$langForm.on("submit", function(e){
			e.preventDefault();
			var song = $(this).data("song");
			var $lang = $("select[name=lang] option:selected", this).val();
			if($(".lyrics").data("lyriclang") == $lang){
				$(this).parents(".modal").modal('hide');
				return;
			}
			var $btn = $("[type=submit]", this);
			$btn.data("html", $btn.html());
			$btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i>');
			$.ajax({
				url: '/fa/userpanel/songs/edit/'.song,
				type: 'GET',
				dataType: "json",
				data: {
					ajax: 1,
					langLyric: $lang
				},
				success: function( data ) {
					$btn.prop("disabled", false).html($btn.data("html"));
					if(data.hasOwnProperty('status')){
						if(data.status){
							if(data.hasOwnProperty('song')){
								var $song = data.song;
								if(!$song.hasOwnProperty('lyrics')){
									$song['lyrics'] = [];
									for(var $x=0;$x<$song['orginalLyric'].length;$x++){
										$song['lyrics'].push({
											"text": "",
											"parent": {
												"id": $song['orginalLyric'][$x]['id'],
												'time': formatTime($song['orginalLyric'][$x]['time']),
												'text': $song['orginalLyric'][$x]['text'],
											}
										});
									}
									
								}
								var lyrics = $song['lyrics'];
								var $leng = $song['orginalLyric'].length;
								var $html = '';
								var $ltr = is_ltr($lang) ? "ltr" : "";
								$html += '<input class=\"lyrics\" type="hidden" name=\"lyric_lang\" value="'+$lang+'"/>'
								for(var $i=0;$i < $leng;$i++){
									var isset = lyrics.hasOwnProperty($i);
									$html += '<div class="row lyrics" data-lyriclang="'+$lang+'"><div class="col-xs-3">';
									$html += '<input value="'+(isset ? lyrics[$i]['id'] : "")+'" name="lyric['+$i+'][id]" class="form-control lyric_id" type="hidden">';
									var $oldRow = getRowByID($song['orginalLyric'][$i]['id']);
									if($song['orginalLang']){
										$html += '<div class="form-group"><input value="'+(isset ? lyrics[$i]['time'] : "")+'" name="lyric['+$i+'][time]" class="form-control lyric_time ltr" type="text"></div>';
									}else{
										var $formGroup = $('.form-group', $oldRow).eq(0);
										$html += '<div class="'+$formGroup.attr('class')+'"><input value="'+$song['orginalLyric'][$i]['time']+'" name="" disabled="" class="form-control ltr" type="text">';
										var $help_block = $formGroup.find('.help-block');
										if($help_block.length){
											$html += $help_block[0].outerHTML;
										}
										$html += '</div>';
									}
									$html += '</div><div class="col-xs-8">';
									if($song['orginalLang']){
										$html += '<div class="form-group"><input value="'+lyrics[$i]['text']+'" name="lyric['+$i+'][text]" class="form-control lyric_text '+$ltr+'" type="text"></div></div></div>';
									}else{
										var $formGroup = $('.form-group', $oldRow).eq(1);
										$html += '<div class="'+$formGroup.attr('class')+'"><input value="'+$song['orginalLyric'][$i]['text']+'" name="" disabled="" class="form-control '+(is_ltr($song['lang']) ? "ltr" : "")+'" type="text">';
										$html += '<input value="'+$song['orginalLyric'][$i]['id']+'" name="lyric['+$i+'][parent]" class="form-control" type="hidden">';
										$html += '<input value="'+(isset ? lyrics[$i]['text'] : "")+'" name="lyric['+$i+'][text]" class="form-control lyric_text '+$ltr+'" type="text">';
										var $help_block = $formGroup.find('.help-block');
										if($help_block.length){
											$html += $help_block[0].outerHTML;
										}
										$html += '</div></div></div>';
									}
								}
								$langForm.parents(".modal").modal('hide');
								$(".lyricFields .lyrics").remove();
								var lyricFields = $($html).appendTo($lyricFields);
								if($song['orginalLang']){
									setEvents(lyricFields);
								}
								var action = form.attr('action');
								action = action.replace(/langLyric=[a-z]{2}/i, "langLyric="+$lang);
								form.attr('action', action);
								console.log(action);
							}
						}else{
							if(data.hasOwnProperty('error')){
								$.growl.error({title:"خطایی وجود دارد!", message:"لطفا خطاهای موجود را برطرف کنید و مجددا اقدام نمایید"});
							}else{
								$.growl.error({title: 'خطا', message: " درخواست شما توسط سرور قبول نشد."});
							}
						}
					}else{
						$.growl.error({title: 'خطا', message: " در حال حاضر سرور پاسخ درخواست شما را به درستی ارسال نمیکند."});
					}
				},
				error: function(){
					$btn.prop("disabled", false).html($btn.data("html"));
					$.growl.error({title: 'خطا', message: " در حال حاضر سرور پاسخ درخواست شما را به درستی ارسال نمیکند."});
				}
			});
		});
	}

	var runLyricLangListerner = function(){
		var lang = location.search.match(/langLyric=([a-z]{2})/i);
		if(!lang)return;
		if(lang[1] != $('input[name=lyric_lang]').val()){
			$("#changeLyricForm select[name=lang]").val(lang[1]);
			$("#changeLyricForm").submit();
		}
	}
	return {
		init: function() {
			mouseHover();
			setEvents(form);
			setTitlesEvents();
			createFieldTranslatedLang();
			changeLyricLang();
			runAlbumListener();
			runGroupListener();
			runLyricLangListerner();
		}
	}
}();
$(function(){
	songEdit.init();
});
