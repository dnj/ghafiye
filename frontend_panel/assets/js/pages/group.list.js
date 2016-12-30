var GroupsLists = function(){
	var runPersonListener = function(){
		$("#groupSearch input[name=person_name]").autocomplete({
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
				$('#groupSearch input[name=person]').val(ui.item.id).trigger('change');
				return false;
			},
			focus: function( event, ui ) {
				$(this).val(ui.item.name);
				$('#groupSearch input[name=person]').val(ui.item.id);
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
			runPersonListener();
		}
	}
}();
$(function(){
	GroupsLists.init();
});
