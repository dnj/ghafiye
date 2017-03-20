import {Router} from "Router";

export class viewLyric{
	public static runLikelistener():void{
		$("#like").on('click', function(e){
			e.preventDefault();
			$.ajax({
				url: Router.url("songs/like/"+$(this).data("song")),
				dataType: "json",
				data: {
					ajax: 1
				},
				success: function( data:{status?:boolean, liked:boolean} ) {
					if(data.hasOwnProperty('status')){
						if(data.status){
							let likes:number = parseInt($(".like-number").text());
							if(data.liked){
								$(".like-icon").attr('class', 'fa fa-heart like-icon');
								$(".like-number").text(++likes);
							}else{
								$(".like-icon").attr('class', 'fa fa-heart-o like-icon');
								$(".like-number").text(--likes);
							}
						}
					}
				}
			});
		});
	}
	public static init():void {
		this.runLikelistener();
	}
}