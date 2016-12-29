var SongsLists = function(){
	var runAlbumListener = function(){
		$("#songsLists input[name='album_name']").autocomplete({
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
				$('#songsLists input[name=album]').val(ui.item.id).trigger('change');
				return false;
			},
			focus: function( event, ui ) {
				$(this).val(ui.item.title);
				$('#songsLists input[name=album]').val(ui.item.id);
				return false;
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<strong>" +item.title+ "</strong>" )
				.appendTo( ul );
		};
	};
	var runGroupListener = function(){
		$("#songsLists input[name=group_name]").autocomplete({
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
				$('#songsLists input[name=group]').val(ui.item.id).trigger('change');
				return false;
			},
			focus: function( event, ui ) {
				$(this).val(ui.item.title);
				$('#songsLists input[name=group]').val(ui.item.id);
				return false;
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<strong>" +item.title+ "</strong>" )
				.appendTo( ul );
		};
	};
	var runPersonListener = function(){
		$("#songsLists input[name=person_name]").autocomplete({
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
				$('#songsLists input[name=person]').val(ui.item.id).trigger('change');
				return false;
			},
			focus: function( event, ui ) {
				$(this).val(ui.item.name);
				$('#songsLists input[name=person]').val(ui.item.id);
				return false;
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<strong>" +item.name+ "</strong>" )
				.appendTo( ul );
		};
	};
	return{
		init:function(){
			runAlbumListener();
			runGroupListener();
			runPersonListener();
		}
	}
}();
$(function(){
	SongsLists.init();
});
