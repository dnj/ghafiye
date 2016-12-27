var albumADD = function () {
	var form = $('.create_form');
	var langs = $("tbody", form).data("langs");
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
	return {
		init: function() {
			mouseHover();
		}
	}
}();
$(function(){
	albumADD.init();
});
