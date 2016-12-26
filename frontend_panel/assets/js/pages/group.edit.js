var groupEdit = function () {
	var form = $('.create_form');
	var langs = $("tbody", form).data("langs");
	var runUserListener = function(){
		$("#addPersonForm input[name=person_name]").autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: "/fa/userpanel/persons",
					dataType: "json",
					data: {
						ajax:1,
						first_name: request.term
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
				$(this).val((ui.item.first_name ? ui.item.first_name : ui.item.first_name));
				$('#addPersonForm input[name=person]').val(ui.item.id).trigger('change');
				return false;
			},
			focus: function( event, ui ) {
				$(this).val((ui.item.first_name ? ui.item.first_name : ui.item.first_name));
				$('#addPersonForm input[name=person]').val(ui.item.id);
				return false;
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<strong>" +(item.first_name ? item.first_name : item.last_name)+ "</strong><small class=\"ltr\">"+item.last_name+"</small><small class=\"ltr\">"+(item.getder == 1 ? "مذکر" : "مونث")+"</small>" )
				.appendTo( ul );
		};
	};
	var mouseHover = function (){
		$(window).ready(function(){
			$(".group-image", form).mouseover(function(){
		        $(this).find(".group-image-buttons").css("display", "block");
		    });
		    $(".group-image", form).mouseout(function(){
		        $(this).find(".group-image-buttons").css("display", "none");
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
	var personDelete = function(e){
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
	var setPersonsEvents = function(container){
		if(!container){
			container = form;
		}
		$(".person-del", container).click(personDelete);
	}
	var createFieldTranslatedLang = function(){
		$("#addTitleform").submit(function(e){
			e.preventDefault();
			var $lang = $("#selectLang option:selected", this).val();
			var $title = $("input[name='title']", this).val();
			var $hasLang = false;
			var $hastitle = false;
			var $lang_title = '';
			$(".titles tr", form).each(function(){
				if($(this).data("lang") == $lang)
					$hasLang = true;
			});
			$(".title", form).each(function(){
				if(this.text.toLowerCase() == $title.toLowerCase()){
					$hastitle = true;
				}
			});
			$(langs).each(function(){
				if(this.value == $lang){
					$lang_title = this.title;
				}
			});
			if($hasLang || $hastitle){
				$.growl.error({title:"خطا!", message:"زبان و یا عنوانی با این مشخصات وجود دارد!"});
			}
			if(!$lang.length || !$title.length){
				$.growl.error({title:"خطا!", message:"داده وارد شده معتبر نیست!"});
			}
			if($lang.length && $title.length && $lang_title.length && !$hasLang && !$hastitle){
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
	var createFieldPersons = function(){
		$("#addPersonForm").submit(function(e){
			e.preventDefault();
			var $person = $("input[name='person']", this).val();
			var $person_name = $("input[name='person_name']", this).val();
			var $hasPerson = false;
			if($(".persons tr[data-person='"+$person+"']", form).length){
				$hasPerson = true;
				$.growl.error({title:"خطا!", message:"شخص قبلا به گروه اضافه شده است"});
			}
			if($person.length && $person_name.length && !$hasPerson){
				var $html = "<tr data-person=\""+$person+"\"><td class=\"column-left\"><input value=\""+$person+"\" name=\"persons[]\" class=\"form-control\" type=\"hidden\"><a href=\"/fa/userpanel/persons/edit/"+$person+"\">"+$person_name+"</a></td>";
			    $html += "<td class=\"center\"><a href=\"#\" class=\"btn btn-xs btn-bricky tooltips person-del\" title=\"\" data-original-title=\"حذف\"><i class=\"fa fa-times\"></i></a></td></tr>";
				var $row = $($html).appendTo($('.persons', form));
				setPersonsEvents($row);
				$("#addPerson").modal('hide');
				this.reset();
			}
		});
	};
	var selectLangValidate = function(){
		$("select[name='group-lang']").change(function(){
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
			setPersonsEvents();
			setTitlesEvents();
			createFieldTranslatedLang();
			createFieldPersons();
			selectLangValidate();
		}
	}
}();
$(function(){
	groupEdit.init();
});
