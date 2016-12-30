var GenreLists = function(){
	var runSongListener = function(){
		$("#GenreSearch input[name=song_name]").autocomplete({
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
				$('#GenreSearch input[name=song]').val(ui.item.id).trigger('change');
				return false;
			},
			focus: function( event, ui ) {
				$(this).val(ui.item.title);
				$('#GenreSearch input[name=song]').val(ui.item.id);
				return false;
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<strong>" +item.title+ "</strong><small>"+item.singer.name+"</small>" )
				.appendTo( ul );
		};
	};
	return{
		init:function(){
			runSongListener();
		}
	}
}();
$(function(){
	GenreLists.init();
});
