import * as $ from "jquery";
import "jquery.growl";
import { Router, AjaxRequest, webuilder } from "webuilder";
import {Source, Result as IResult, Crawler} from "../Crawler";
import {paginate} from "../../classes/paginate";
export class artist implements Source{
	public search(data:any):Result[]{
		let results:Result[] = [];
		for(const artist of data.items){
			let result = new Result();
			result.id = artist.id;
			result.title = artist.name;
			result.isQueued = artist.isQueued;
			result.isExist = artist.isExist;
			result.image = artist.avatar;
			result.rating = artist.rating;
			result.country = artist.country;
			results.push(result);
		}
		return results;
	}
	public goTo(page:number){
		const name = $('.crawler-add-form input[name=name]').val();
		AjaxRequest({
			url: Router.url(`userpanel/crawler/queue/search?ajax=1&page=${page}`),
			type: 'post',
			data: {
				type: 1,
				name: name
			},
			success: (data: webuilder.AjaxResponse) => {
				const results = this.search(data);
				Crawler.showResultForSearch(results);
				Crawler.runPaginate(data.current_page, data.items_per_page, data.total_items, '1');
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
	type:"artist";
	public id:number;
	public title:string;
	public isQueued:boolean;
	public isExist:boolean;
	public image?:string;
	public country:string;
	public rating:number;
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
				type: 1
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
	public getDescription():string{
		return this.country;
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
			<div class="col-sm-3 pull-right">
				<a class="btn btn-sm btn-warning btn-return" title="بازگشت">
					<i class="fa fa-hand-o-right"></i> بازگشت
				</a>
			</div>
		</div>
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
					<li> کشور : ${this.country}</li>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-3">
				<button class="btn btn-success btn-block ${this.canSelect() ? 'btn-select' : ''}" ${!this.canSelect() ? 'disabled=""' : ''} type="submit">افزودن</button>
			</div>
			<div class="col-sm-3 col-sm-offset-3">
				<button class="btn btn-block btn-primary btn-albums">آلبوم ها</button>
			</div>
			<div class="col-sm-3">
				<button class="btn btn-block btn-primary btn-tracks">آهنگ ها</button>
			</div>
		</div>
		`;
		return html;
	}
	public setEventForResultInfo(){
		const result = this;
		$('.step.result-info .btn-albums').on('click', function(e) {
			e.preventDefault();
			const html = $(this).html();
			$(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
			$(this).prop('disabled', true);
			const that = $(this);
			AjaxRequest({
				url: Router.url('userpanel/crawler/queue/search?ajax=1'),
				type: 'post',
				data: {
					artist: result.id,
					type: 4
				},
				success: (data: webuilder.AjaxResponse) => {
					const results = Crawler.sources.album.search(data);
					Crawler.showResultForSearch(results);
					Crawler.runPaginate(data.current_page, data.items_per_page, data.total_items, '4');
					$('.container .step.search.paging').data('result', result);
				},
				error: function(error:webuilder.AjaxError){
					$.growl.error({
						title:"خطا",
						message:'متاسفانه خطایی بوجود آمده'
					});
					that.html(html);
					that.prop('disabled', false);
				}
			});
		});
		$('.step.result-info .btn-tracks').on('click', function(e) {
			e.preventDefault();
			const html = $(this).html();
			$(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
			$(this).prop('disabled', true);
			const that = $(this);
			AjaxRequest({
				url: Router.url('userpanel/crawler/queue/search?ajax=1'),
				type: 'post',
				data: {
					artist: result.id,
					type: 3
				},
				success: (data: webuilder.AjaxResponse) => {
					const results = Crawler.sources.track.search(data);
					Crawler.showResultForSearch(results);
					Crawler.runPaginate(data.current_page, data.items_per_page, data.total_items, '3');
					$('.container .step.search.paging').data({
						result: result,
						searchBy: 'artist'
					});
				},
				error: function(error:webuilder.AjaxError){
					$.growl.error({
						title:"خطا",
						message:'متاسفانه خطایی بوجود آمده'
					});
					that.html(html);
				}
			});
		});
		$('.step.result-info .btn-select').on('click', function(e){
			e.preventDefault();
			const html = $(this).html();
			$(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
			$(this).prop('disabled', true);
			result.select($(this), html);
		});
		$('.step.result-info .btn-return').on('click', (e) => {
			Crawler.goToStep('search');
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