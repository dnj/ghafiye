import * as $ from "jquery";
import "jquery.growl";
import { Router, AjaxRequest, webuilder } from "webuilder";
import {Source, Result as IResult, Crawler} from "../Crawler";
export class album implements Source{
	public search(data:any):Result[]{
		let results:Result[] = [];
		for(const album of data.items){
			let result = new Result();
			result.id = album.id;
			result.title = album.name;
			result.isQueued = album.isQueued;
			result.isExist = album.isExist;
			result.image = album.image;
			result.rating = album.rating;
			result.artistTitle = album.artist_name;
			results.push(result);
		}
		return results;
	}
}
class Result implements IResult{
	type:"album";
	public id:number;
	public title:string;
	public isQueued:boolean;
	public isExist:boolean;
	public image?:string;
	public rating:number;
	public artistTitle:string;
	constructor(){
		const that = this;
		$('.step.result-info').on('result.shown', function(){
			if(that == $(this).data('result')){
				that.setEventForResultInfo();
			}
		});
	}
	public select():void{
		AjaxRequest({
			url: Router.url('userpanel/crawler/queue/add?ajax=1'),
			type: 'post',
			data: {
				MMID: this.id,
				type: 4
			},
			success: (data: webuilder.AjaxResponse) => {
				$.growl.notice({
					title: "!موفق",
					message: "درخواست شما با موفقیت ثبت شد ."
				});
				setTimeout(window.location.href = window.location.href, 2000);
			},
			error: function(error:webuilder.AjaxError){
				$.growl.error({
					title:"خطا",
					message:'متاسفانه خطایی بوجود آمده'
				});
			}
		});
	}
	public getDescription():string{
		return this.artistTitle;
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
					<li> نام خواننده : ${this.artistTitle}</li>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-3">
				<button class="btn btn-success btn-block ${this.canSelect() ? 'btn-select' : ''}" ${!this.canSelect() ? 'disabled=""' : ''} type="submit">افزودن</button>
			</div>
			<div class="col-sm-3 col-sm-offset-6">
				<button class="btn btn-block btn-primary btn-tracks">آهنگ ها</button>
			</div>
		</div>
		`;
		return html;
	}
	public setEventForResultInfo(){
		$('.step.result-info .btn-tracks').on('click', (e) => {
			e.preventDefault();
			AjaxRequest({
				url: Router.url('userpanel/crawler/queue/search?ajax=1'),
				type: 'post',
				data: {
					album: this.id,
					type: 3
				},
				success: (data: webuilder.AjaxResponse) => {
					const results = Crawler.sources.track.search(data);
					Crawler.showResultForSearch(results);
				},
				error: function(error:webuilder.AjaxError){
					$.growl.error({
						title:"خطا",
						message:'متاسفانه خطایی بوجود آمده'
					});
				}
			});
		});
		const that = this;
		$('.step.result-info .btn-select').on('click', function(e){
			e.preventDefault();
			that.select();
			$(this).prop('disabled', true);
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