import * as $ from "jquery";
import "jquery-ui/ui/widgets/autocomplete";
import {Router} from "webuilder";

export class HomePageSearch{
	private static $form = $(".searchbox");
	private static $input= $("input[name=word]", HomePageSearch.$form);
	public static runWordSeachListener(): void{
		this.$input.autocomplete({
			source: function( request:{term:string}, response:Function) {
				$.ajax({
					url: Router.url(`search/${request.term}`),
					dataType: "json",
					data: {
						ajax:1
					},
					success: function( data:{status:boolean, items:any[]}) {
						if(data.status){
							response( data.items );
						}
					}
				});
			},
			select: function( event, ui ) {
				if(typeof ui.item != 'undefined'){
					switch(ui.item.type){
						case("person"):
							window.location.href = Router.url(ui.item.encodedName);
							break;
						case("song"):
							window.location.href = Router.url(ui.item.singer.encodedName+'/'+ui.item.encodedTitle);
							break;
					}
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
		let $instance :{_renderItem:any, _renderMenu:any, _resizeMenu:any} = this.$input.data( "ui-autocomplete" );
		$instance._renderItem = function( ul:any, item:any ) {
			let html:string;
			switch(item.type){
				case("person"):
					const avatar = item.avatar != null ? item.avatar : 'storage/public/songs/default-image.png';
					const defaultClass = item.avatar == null ? "default" : "";
					html = `<div class="col-xs-4 col-sm-3 col-md-2 avatar ${defaultClass}"><img src="/packages/ghafiye/${avatar}"/></div><div class="col-xs-8 col-sm-9 col-md-10 title"><strong>${item.name}</strong></div>`;
					break;i
				case("song"):
					const image = item.image != null ? item.image : 'storage/public/songs/default-image.png';
					const defaultClass = item.image == null ? "default" : "";
					html = `<div class="col-xs-4 col-sm-3 col-md-2 avatar ${defaultClass}"><img src="/packages/ghafiye/${image}"/></div><div class="col-xs-8 col-sm-9 col-md-10 title"><strong>${item.title}</strong><small>${item.singer.name}</small></div>`;
					break;
			}
			return $( "<li>" )
				.append( html )
				.appendTo( ul );
		};
		$instance._renderMenu = function( ul:any, items:any[] ) {
			$.each( items, (key,item) => {
				this._renderItemData( ul, item );
			});
			$( ul ).addClass('search-autocomplete');
			if(items.length){
				$( '<li class="ui-menu-item all-result"><a href="'+Router.url('search/'+HomePageSearch.getInput().val())+'">مشاهده همه نتایج</a></li>' ).prependTo(ul).on('click',function(){
					window.location.href = $('a', this).attr('href');
				});
			}

		}
		$instance._resizeMenu= function( ul:any, items:any[] ) {
			this.menu.element.outerWidth( HomePageSearch.getInput().outerWidth() );
		}
	}
	public static runFormSubmit() :void{
		this.$form.submit((e) => {
			e.preventDefault();
			window.location.href = Router.url("search/"+this.$input.val());
		});
	}
	public static getInput(){
		return this.$input;
	}
	public static init():void{
		this.runFormSubmit();
		this.runWordSeachListener();
	}
}