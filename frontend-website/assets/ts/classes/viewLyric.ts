import {Router} from "webuilder";
import "bootstrap/js/tooltip";
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
	private static runChangeLangListener():void{
		$('.translations select.selectpicker option').on('click', function(){
			console.log($(this).hasClass('ltr'));
			if($(this).hasClass('ltr')){
				$(this).parents('.selectpicker').addClass('ltr');
			}else{
				$(this).parents('.selectpicker').removeClass('ltr');
			}
			const lang = $('section.text').data('lang');
			if(lang != $(this).val()){
				window.location.href = $(this).data('link');
			}
		});
	}
	public static init():void {
		this.runLikelistener();
		$(function () {
			$('.tooltips').tooltip()
		})
		if($('.translations select.selectpicker').length){
			$('.translations select.selectpicker').val($('section.text').data('lang'));
			this.runChangeLangListener();
		}
	}
}