var albumEdit = function () {
	var form = $('.create_form');
	var langs = $("tbody", form).data("langs");
	var runUserListener = function(){
		$("#addSongForm input[name=song_name]").autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: "/fa/userpanel/songs",
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
				$('#addSongForm input[name=song]').val(ui.item.id).trigger('change');
				return false;
			},
			focus: function( event, ui ) {
				$(this).val(ui.item.title);
				$('#addSongForm input[name=song]').val(ui.item.id);
				return false;
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<strong>" +item.title+ "</strong><small>"+item.singer.name+"</small>" )
				.appendTo( ul );
		};
	};
	var mouseHover = function (){
		$(window).ready(function(){
			$(".album-image", form).mouseover(function(){
		        $(this).find(".album-image-buttons").css("display", "block");
		    });
		    $(".album-image", form).mouseout(function(){
		        $(this).find(".album-image-buttons").css("display", "none");
		    });
		});
	};
	var titleDelete = function(e){
		e.preventDefault();
		var $lang_count = 0;
		$(".titles tr", form).each(function(){
			$lang_count++;
		});
		if($lang_count > 1){
			$(this).parents("tr").remove();
		}else{
			$.growl.error({title:"خطا!", message:"باید حداقل یک عنوان وجود داشته باشد!"});
		}
	}
	var songDelete = function(e){
		e.preventDefault();
		$(this).parents("tr").remove();
	}
	var titleEditSave = function(e, params) {
		var $lang = $(this).data('lang');
		$(this).parents("tr").find("input[name='titles["+$lang+"]']").val(params.newValue);
	}
	var setTitlesEvents = function(container){
		if(!container){
			container = form;
		}
		$(".title-del", container).click(titleDelete);
		$.fn.editable.defaults.mode = "inline";
		$('.title', container).editable().on('save', titleEditSave);
	}
	var setsongsEvents = function(container){
		if(!container){
			container = form;
		}
		$(".song-del", container).click(songDelete);
	}
	var createFieldTranslatedLang = function(){
		$("#addTitleform").submit(function(e){
			e.preventDefault();
			var $lang = $("#selectLang option:selected", this).val();
			var $title = $("input[name='title']", this).val();
			var $hasLang = false;
			var $lang_title = '';
			if(!$lang.length || !$title.length){
				$.growl.error({title:"خطا!", message:"داده وارد شده معتبر نیست!"});
			}
			if($(".titles tr[data-lang='"+$lang+"']", form).length){
				$hasLang = true;
				$.growl.error({title:"خطا!", message:"ترجمه زبان انتخاب شده قبلا اضافه شده است"});
			}
			$(langs).each(function(){
				if(this.value == $lang){
					$lang_title = this.title;
				}
			});
			if($lang.length && $title.length && $lang_title.length && !$hasLang){
				var $html = "<tr data-lang=\""+$lang+"\"><td class=\"column-left\"><input value=\""+$title+"\" name=\"titles["+$lang+"]\" class=\"form-control\" type=\"hidden\">"+$lang_title+"</td>";
			    $html += "<td class=\"column-right\"><a href=\"#\" data-lang=\""+$lang+"\" data-type=\"text\" data-pk=\"1\" data-original-title=\""+$title+"\" class=\"editable editable-click title\" style=\"display: inline;\">"+$title+"</a></td>";
			    $html += "<td class=\"center\"><a href=\"#\" class=\"btn btn-xs btn-bricky tooltips title-del\" title=\"\" data-original-title=\"حذف\"><i class=\"fa fa-times\"></i></a></td></tr>";
				var $row = $($html).appendTo($('.titles', form));
				setTitlesEvents($row);
				$("#addTitle").modal('hide');
				this.reset();
			}
		});
	};
	var createFieldsongs = function(){
		$("#addSongForm").submit(function(e){
			e.preventDefault();
			var $song = $("input[name='song']", this).val();
			var $song_name = $("input[name='song_name']", this).val();
			var $hassong = false;
			if($(".songs tr[data-song='"+$song+"']", form).length){
				$hassong = true;
				$.growl.error({title:"خطا!", message:"آهنگ قبلا به آلبوم اضافه شده است!"});
			}
			if($song.length && $song_name.length && !$hassong){
				var $html = "<tr data-song=\""+$song+"\"><td class=\"column-left\"><input value=\""+$song+"\" name=\"songs[]\" class=\"form-control\" type=\"hidden\"><a href=\"/fa/userpanel/songs/edit/"+$song+"\">"+$song_name+"</a></td>";
			    $html += "<td class=\"center\"><a href=\"#\" class=\"btn btn-xs btn-bricky tooltips song-del\" title=\"\" data-original-title=\"حذف\"><i class=\"fa fa-times\"></i></a></td></tr>";
				var $row = $($html).appendTo($('.songs', form));
				setsongsEvents($row);
				$("#addSong").modal('hide');
				this.reset();
			}
		});
	};
	var selectLangValidate = function(){
		$("select[name='album-lang']").change(function(){
			var selected = $("option:selected", this).val();
			var $lang = $("option:selected", this).text()
			if(!$(".titles tr[data-lang='"+selected+"']", form).length){
				$.growl.error({title:"خطا!", message:"باید حتما ترجمه ای با زبان "+$lang+" وجود داشته باشد!"});
			}
		});
	}
	return {
		init: function() {
			runUserListener();
			mouseHover();
			setsongsEvents();
			setTitlesEvents();
			createFieldTranslatedLang();
			createFieldsongs();
			selectLangValidate();
		}
	}
}();
$(function(){
	albumEdit.init();
});
