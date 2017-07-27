import * as $ from "jquery";
import "jquery.growl";
import { Router, AjaxRequest, webuilder } from "webuilder";
import {Source, Result as IResult, Crawler} from "../Crawler";
export class track implements Source{
	public search(data:any):Result[]{
		let results:Result[] = [];
		for(const track of data.items){
			let result = new Result();
			result.id = track.id;
			result.title = track.name;
			result.isQueued = track.isQueued;
			result.isExist = track.isExist;
			result.image = track.image;
			result.rating = track.rating;
			result.artistTitle = track.artist_name;
			result.albumTitle = track.album_name;
			result.genres = track.genres;
			results.push(result);
		}
		return results;
	}
	public goTo(page:number, data:any){
		const name = $('.crawler-add-form input[name=name]').val();
		AjaxRequest({
			url: Router.url(`userpanel/crawler/queue/search?ajax=1&page=${page}`),
			type: 'post',
			data: data,
			success: (data: webuilder.AjaxResponse) => {
				const results = this.search(data);
				Crawler.showResultForSearch(results);
				Crawler.runPaginate(data.current_page, data.items_per_page, data.total_items, '3');
			},
			error: function(error:webuilder.AjaxError){
				$.growl.error({
					title:"خطا",
					message:'متاسفانه خطایی بوجود آمده'
				});
			}
		});
	}
}
class Result implements IResult{
	type:"track";
	public id:number;
	public title:string;
	public isQueued:boolean;
	public isExist:boolean;
	public image?:string;
	public rating:number;
	public artistTitle:string;
	public albumTitle:string;
	public genres:any;
	constructor(){
		const that = this;
		$('.step.result-info').on('result.shown', function(){
			if(that == $(this).data('result')){
				that.setEventForResultInfo();
			}
		});
	}
	public select(btn:JQuery, html:string):void{
		AjaxRequest({
			url: Router.url('userpanel/crawler/queue/add?ajax=1'),
			type: 'post',
			data: {
				MMID: this.id,
				type: 3
			},
			success: (data: webuilder.AjaxResponse) => {
				$.growl.notice({
					title: "!موفق",
					message: "درخواست شما با موفقیت ثبت شد ."
				});
				btn.html(html)
				btn.prop('disabled', true);
				setTimeout(Crawler.goToStep('search'), 2000);
			},
			error: function(error:webuilder.AjaxError){
				$.growl.error({
					title:"خطا",
					message:'متاسفانه خطایی بوجود آمده'
				});
				btn.html(html);
				btn.prop('disabled', false);
			}
		});
	}
	public getGeneres():string{
		let genres:string = '';
		for(const genre of this.genres){
			genres += `<li data-id="${genre.id}"> ${genre.name} </li>`;
		}
		return genres;
	}
	public getDescription():string{
		const description = `
			<p>${this.artistTitle}</p>
			سبک ها : 
			<ul class="genres">
				${this.getGeneres()}
			</ul>
		`;
		return description;
	}
	public getRate():string{
		let result = this.rating / 20;
		let html = '<div class="rating">';
		for(let i = 1; i <= 5; i++){
			html += `<i class="fa ${i < result ? 'fa-star' : 'fa-star-o'}"></i>`;
		}
		html += '</div>'
		return html;
	}
	public show():string{
		const html = `
		<div class="row">
			<div class="col-sm-3">
				<img class="img-responsive img-thumbnail result-image" src="${this.image}"/>
			</div>
			<div class="col-sm-9">
				<span class="h1 title">${this.title}</span>
				<div class="row">
					<div class="col-sm-6">
						${this.getStatus()}
					</div>
					<div class="col-sm-6">
						${this.getRate()}
					</div>
				</div>
				<ul>
					<li> شناسه musixmatch : ${this.id}</li>
					<li> سبک ها : <ul>${this.getGeneres()}</ul> </li>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-3">
				<button class="btn btn-success btn-block ${this.canSelect() ? 'btn-select' : ''}" ${!this.canSelect() ? 'disabled=""' : ''} type="submit">افزودن</button>
			</div>
		</div>
		`;
		return html;
	}
	public setEventForResultInfo(){
		const that = this;
		$('.step.result-info .btn-select').on('click', function(e){
			e.preventDefault();
			const html = $(this).html()
			$(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
			$(this).prop('disabled', true);
			that.select($(this), html);
		});
	}
	public getStatus():string{
		if(this.isExist){
			return '<span class="label label-success">موجود</span>';
		}
		if(this.isQueued){
			return '<span class="label label-inverse">در صف</span>';
		}
		return '<span class="label label-danger">ناموجود</span>';
	}
	public canSelect():boolean{
		return !(this.isExist || this.isQueued);
	}
}