var genreEdit = function () {
	var form = $('.create_form');
	var langs = $(".langs", form).data("langs");
	var titleDelete = function(e){
		e.preventDefault();
		var $lang_count = 0;
		if($(".langs tr", form).length > 1){
			$(this).parents("tr").remove();
		}else{
			$.growl.error({title:"خطا!", message:"باید حداقل یک عنوان وجود داشته باشد!"});
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
		$(".title-del", container).click(titleDelete);
		$.fn.editable.defaults.mode = "inline";
		$('.title', container).editable().on('save', titleEditSave);
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
			if($(".table-titles tr[data-lang='"+$lang+"']", form).length){
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
				var $row = $($html).appendTo($('.table-titles', form));
				setTitlesEvents($row);
				$("#addTitle").modal('hide');
				this.reset();
			}
		});
	};
	return {
		init: function() {
			setTitlesEvents();
			createFieldTranslatedLang();
		}
	}
}();
$(function(){
	genreEdit.init();
});
