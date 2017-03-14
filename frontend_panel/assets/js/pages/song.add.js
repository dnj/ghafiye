var ADDSong = function () {
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
	var runUserListener = function(){
		$("#addPersonForm input[name=person_name]").autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: "/fa/userpanel/persons",
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
				$(this).val(ui.item.name);
				$('#addPersonForm input[name=person]').val(ui.item.id).trigger('change');
				return false;
			},
			focus: function( event, ui ) {
				$(this).val(ui.item.name);
				$('#addPersonForm input[name=person]').val(ui.item.id);
				return false;
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<strong>" +item.name+"</strong>" )
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
		songTitleNotice();
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
		shiftIndex($row);
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
			var name = $row.find(".lyric_text").attr('name');
			var index = parseInt(name.match(/(\d+)/)[0]) + 1;
			for(var i = eq + 1; i < $lyrics.length ;i++, index++){
				console.log($lyrics.eq(i), index);
				$lyrics.eq(i).find(".lyric_id").attr('name', 'lyric['+index+'][id]');
				$lyrics.eq(i).find(".lyric_time").attr('name', 'lyric['+index+'][time]');
				$lyrics.eq(i).find(".lyric_text").attr('name', 'lyric['+index+'][text]');
			}
		}
	}
	
	var getRowByID = function(id){
		return $('input.lyric_id[value="'+id+'"]', form).parents('.lyrics');
	}
	var inputDirection = function(){
		var langSelector = $("select[name=lang]", form);
		langSelector.on("change", function(){
			var lang = $('option:selected', this).val();
			var ltr = is_ltr(lang);
			if(ltr){
				$(".lyric_text", $lyricFields).addClass("ltr");
				$("input[name=title]").addClass("ltr");
			}else{
				$(".lyric_text", $lyricFields).removeClass("ltr");
				$("input[name=title]").removeClass("ltr");
			}
		});
		langSelector.trigger("change");
	}
	var ValidateLyrics = function(){
		form.on("submit", function(){
			$(".lyric_text", this).each(function(){
				if(!$(this).val()){
					$(this).parents(".lyrics").remove();
				}
			});
		});
	}
	var personDelete = function(e){
		e.preventDefault();
		var $person_count = 0;
		$(".persons tr", form).each(function(){
			$person_count++;
		});
		if($person_count > 1){
			$(this).parents("tr").remove();
		}else{
			$.growl.error({title:"خطا!", message:"باید حداقل یک شخص وجود داشته باشد!"});
		}
	}
	var setPersonsEvents = function(container){
		var $inputs = $('.person-primary', container ? container : form);
		if(container){
			$inputs.iCheck({
				checkboxClass: 'icheckbox_minimal-grey',
				increaseArea: '10%' // optional
			});
		}
		$inputs.on("ifChecked", function(event){
			var $tr = $(this).parents('tr');
			var $val = $(".person-role", $tr).val();
			$(".persons .person-role", form).each(function(){
				var $this = $(this);
				var $itemTr = $this.parents('tr');
				if($this.val() == $val && !$itemTr.is($tr)){
					$('.person-primary', $itemTr).iCheck('uncheck');
				}
			});
		});
		$(".person-del", container ? container : form).on('click', personDelete);
		$(".person-role", container ? container : form).on("change", function(){
			$(".person-primary", $(this).parents('tr')).iCheck('uncheck');
		});
	}
	var createFieldPersons = function(){
		$("#addPersonForm").submit(function(e){
			e.preventDefault();
			var $person = $("input[name='person']", this).val();
			var $person_name = $("input[name='person_name']", this).val();
			var $hasPerson = false;
			if($(".persons tr[data-person='"+$person+"']", form).length){
				$hasPerson = true;
				$.growl.error({title:"خطا!", message:"شخص قبلا اضافه شده است"});
			}
			if($person.length && $person_name.length && !$hasPerson){
				var $html = "<tr data-person=\""+$person+"\"><td class=\"column-left\"><input value=\""+$person+"\" name=\"persons["+$person+"][id]\" class=\"form-control\" type=\"hidden\"><a href=\"/fa/userpanel/persons/edit/"+$person+"\" target=\"_blank\">"+$person_name+"</a></td>";
				$html += '<td><div class="form-group"><select name="persons['+$person+'][role]" class="form-control person-role"><option value="1">خواننده</option><option value="2">نویسنده</option><option value="3">آهنگساز</option></select></div></td>';
			    $html += '<td class="center"><div class="form-group"><div class="checkbox"><label><input type="checkbox" name="persons['+$person+'][primary]" value="1" class="grey person-primary"></label></div></div>';
			    $html += "<td class=\"center\"><a href=\"#\" class=\"btn btn-xs btn-bricky tooltips person-del\" title=\"\" data-original-title=\"حذف\"><i class=\"fa fa-times\"></i></a></td></tr>";
				var $row = $($html).appendTo($('.persons', form));
				setPersonsEvents($row);
				$("#addPerson").modal('hide');
				this.reset();
			}
		});
	};
	return {
		init: function() {
			mouseHover();
			setEvents(form);
			runAlbumListener();
			runGroupListener();
			runUserListener();
			inputDirection();
			ValidateLyrics();
			createFieldPersons();
			setPersonsEvents();
		}
	}
}();
$(function(){
	ADDSong.init();
});
