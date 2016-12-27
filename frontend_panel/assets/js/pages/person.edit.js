var personEdit = function () {
	var form = $('.create_form');
	var langs = $("tbody", form).data("langs");
	var mouseHover = function (){
		$(window).ready(function(){
			$(".person-image", form).mouseover(function(){
		        $(this).find(".person-image-buttons").css("display", "block");
		    });
		    $(".person-image", form).mouseout(function(){
		        $(this).find(".person-image-buttons").css("display", "none");
		    });
		});
	};
	var nameDelete = function(e){
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
	var nameEditSave = function(e, params) {
		var $lang = $(this).data('lang');
		$(this).parents("tr").find("input[name='names["+$lang+"]']").val(params.newValue);
	}
	var setNamesEvents = function(container){
		if(!container){
			container = form;
		}
		$(".lang-del", container).click(nameDelete);
		$.fn.editable.defaults.mode = "inline";
		$('.name', container).editable().on('save', nameEditSave);
	}
	var createFieldTranslatedLang = function(){
		$("#addnameform").submit(function(e){
			e.preventDefault();
			var $lang = $("#selectLang option:selected", this).val();
			var $name = $("input[name='name']", this).val();
			var $hasLang = false;
			var $hasName = false;
			var $lang_name = '';
			$(".langs tr", form).each(function(){
				if($(this).data("lang") == $lang)
					$hasLang = true;
			});
			$(".name", form).each(function(){
				if(this.text.toLowerCase() == $name.toLowerCase()){
					$hasName = true;
				}
			});
			$(langs).each(function(){
				if(this.value == $lang){
					$lang_name = this.title;
				}
			});
			if($hasLang || $hasName){
				$.growl.error({title:"خطا!", message:"زبان و یا نامی با این مشخصات وجود دارد!"});
			}
			if(!$lang.length || !$name.length){
				$.growl.error({title:"خطا!", message:"داده وارد شده معتبر نیست!"});
			}
			if($lang.length && $name.length && $lang_name.length && !$hasLang && !$hasName){
				var $html = "<tr data-lang=\""+$lang+"\"><td class=\"column-left\"><input value=\""+$name+"\" name=\"names["+$lang+"]\" class=\"form-control\" type=\"hidden\">"+$lang_name+"</td>";
			    $html += "<td class=\"column-right\"><a href=\"#\" data-lang=\""+$lang+"\" data-type=\"text\" data-pk=\"1\" data-original-title=\""+$name+"\" class=\"editable editable-click name\" style=\"display: inline;\">"+$name+"</a></td>";
			    $html += "<td class=\"center\"><a href=\"#\" class=\"btn btn-xs btn-bricky tooltips lang-del\" title=\"\" data-original-title=\"حذف\"><i class=\"fa fa-times\"></i></a></td></tr>";
				var $row = $($html).appendTo($('.langs', form));
				setNamesEvents($row);
				$("#addName").modal('hide');
				this.reset();
			}
		});
	}
	return {
		init: function() {
			mouseHover();
			setNamesEvents();
			createFieldTranslatedLang();
		}
	}
}();
$(function(){
	personEdit.init();
});
