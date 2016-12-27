var groupEdit = function () {
	var form = $('.create_form');
	var langs = $("tbody", form).data("langs");
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
	return {
		init: function() {
			mouseHover();
		}
	}
}();
$(function(){
	groupEdit.init();
});
