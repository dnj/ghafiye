import * as $ from "jquery";
import Group from "./pages/Group";
import Album from "./pages/Album";
import Genre from "./pages/Genre";
import Person from "./pages/Person";
import Song from "./pages/Song";
import viewError from "./classes/viewError";
export default class Main{
	private static error:viewError[] = [];
	public static addError(error:viewError):void{
		Main.error.push(error);
	}
	public static getErrorHTML():void{
		for(const error of Main.error){
			let alert:any = [];
			let data = error.getData();
			if(!(data instanceof Array)){
				data = [];
			}
			switch(error.getType()){
				case(error.FATAL):
					alert['type'] = 'danger';
					if(!alert['title']){
						alert['title'] = 'خطا';
					}
					break;
				case(error.WARNING):
					alert['type'] = 'warning';
					if(!alert['title']){
						alert['title'] = 'اخطار';
					}
					break;
				case(error.NOTICE):
					alert['type'] = 'info';
					if(!alert['title']){
						alert['title'] = 'توجه';
					}
					break;
			}
			const html = `
				<div class="row">
					<div class="col-xs-12">
						<div class="alert alert-block alert-${alert['type']}">
							<button data-dismiss="alert" class="close" type="button">×</button>
							<h4 class="alert-heading"><i class="fa fa-times-circle"></i> ${alert['title']}</h4>
							<p>${error.getMessage()}</p>
						</div>
					</div>
				</div>
			`;
			$('.panel.panel-default').parents('.row').before(html);
		}
	}
}
$(function(){
	Album.initIfNeeded();
	Group.initIfNeeded();
	Genre.initIfNeeded();
	Person.initIfNeeded();
	Song.initIfNeeded();
});