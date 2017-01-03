var viewLyric = function(){
	var runLikelistener = function(){
		$("#like").click(function(e){
			e.preventDefault();
			$.ajax({
				url: "/fa/songs/like/"+($(this).data("song")),
				dataType: "json",
				data: {
					ajax: 1
				},
				success: function( data ) {
					if(data.hasOwnProperty('status')){
						if(data.status){
							var $likes = $(".like-number").text();
							if(data.liked){
								$(".like-icon").attr('class', 'fa fa-heart like-icon');
								$(".like-number").text(++$likes);
							}else{
								$(".like-icon").attr('class', 'fa fa-heart-o like-icon');
								$(".like-number").text(--$likes);
							}
						}
					}
				}
			});
		});
	}
	return{
		init: function(){
			runLikelistener();
		}
	}
}();
$(function(){
	viewLyric.init();
});
