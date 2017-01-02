var HomePageSearch = function(){
	var form = $(".searchbox");
	var runWordSeachListener = function(){
		var $input= $("input[name=word]", form);
		$input.autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: "/fa/search",
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
				switch(ui.item.type){
					case("person"):
						window.location.href = 'fa/'+ui.item.name;
						break;
					case("song"):
						window.location.href = 'fa/'+ui.item.singer.name+'/'+ui.item.title;
						break;
				}
				return false;
			},
			open:function(){
				$(this).addClass('active');
			},
			close:function(){
				$(this).removeClass('active');
			},
			search: function( event, ui ) {
				$(".form-control-icon", $(this).parent()).attr('class', 'fa fa-spinner fa-pulse fa-3x fa-fw form-control-icon');
			},
			response: function( event, ui ) {
				$(".form-control-icon", $(this).parent()).attr('class', 'fa fa-search form-control-icon');
			}
		})
		var $instance = $input.autocomplete( "instance" );
		$instance._renderItem = function( ul, item ) {
			var $html = '';
			switch(item.type){
				case("person"):
					$html = "<div class=\"col-md-1\"><img src=\"/packages/ghafiye/"+item.avatar+"\"/></div><div class=\"col-md-11\"><strong>" +item.name+ "</strong></div>";
					break;
				case("song"):
					$html = "<div class=\"col-md-1\"><img src=\"/packages/ghafiye/"+item.image+"\"/></div><div class=\"col-md-11\"><strong>" +item.title+ "</strong><small>"+item.singer.name+"</small></div>";
					break;
			}
			return $( "<li>" )
				.append( $html )
				.appendTo( ul );
		};
		$instance._renderMenu= function( ul, items ) {
			var that = this;
			$.each( items, function( index, item ) {
				that._renderItemData( ul, item );
			});
			$( ul ).addClass('search-autocomplete');
		}
	};
	return{
		init:function(){
			runWordSeachListener();
		}
	}
}();
$(function(){
	HomePageSearch.init();
});
