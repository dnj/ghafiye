import * as $ from "jquery";
import "jquery.growl";
import { Router, AjaxRequest, webuilder } from "webuilder";
import "bootstrap/js/modal";
import "bootstrap/js/popover";
import "jquery-bootstrap-checkbox";
import {artist} from "./crawler/artist.source";
import {album} from "./crawler/album.source";
import {track} from "./crawler/track.source";
export interface Result{
	type: "artist" | "track" | "album";
	id:number;
	title:string;
	select():void;
	isQueued:boolean;
	isExist:boolean;
	image?:string;
	rating:number;
	getDescription():string;
	getRate():string;
	show():string;
	getStatus():string;
	canSelect():boolean;
}
export interface Source{
	search(data:any):Result[];
}
export class Crawler{
	private static $form:JQuery;
	public static sources:any;
	private static typeListener():void{
		$('.changeQueueType', Crawler.$form).on('click', function(e){
			e.preventDefault();
			$('input[name=type]', Crawler.$form).val($(this).data('value'));
			const html = $(this).html() + ' <span class="caret"></span>';
			$('.queueType').html(html);
		});
	}
	public static goToStep(step:string){
		$('.step.active').removeClass('active');
		$('.step.'+step).addClass('active');
		$(`.anchor a[data-step="${step}"]`).removeClass('disabled').addClass('selected');
	}
	public static showResultForSearch(results: Result[]){
		Crawler.goToStep('search');
		$('.results').html('');
		function setEventsForCards($card:JQuery){
			$('.btn-info', $card).on('click', function(){
				Crawler.goToStep('result-info');
				const result = $(this).parents('.card').data('result');
				Crawler.showResultInformation(result);
			});
			$('.btn-select', $card).on('click', function(){
				Crawler.goToStep('import');
				const result = $(this).parents('.card').data('result');
				result.select();
			});
		}
		let i = 0;
		const $results = $('.results');
		for(const result of results){
			if(i % 4 == 0){
				$results.append('<div class="row"></div>');
			}
			i++;
			const html = `<div class="col-sm-3">
				<div class="card">
					<div class="card-image">
						<img class="img-responsive" src="${result.image}">
						<a class="btn btn-lg ${result.canSelect() ? 'btn-select' : ''}" ${result.canSelect() ? '' : 'disabled=""'}><i class="fa fa-plus"></i></a>
						${result.getRate()}
						${result.getStatus()}
					</div>
					<div class="card-content">
						<p class="h4 card-title">${result.title}</p>
						<div class="card-text">
							${result.getDescription()}
						</div>
					</div>
					<div class="card-footer">
						<button class="btn btn-block btn-sm btn-teal btn-info">نمایش جزئیات</button>
					</div>
				</div>
			</div>`;
			const $card = $(html).appendTo($('.row', $results).last()).find('.card');
			$card.data('result', result);
		}
		setEventsForCards($results);
	}

	private static showResultInformation(result:Result){
		Crawler.goToStep('result-info');
		const html = result.show();
		$('.step.result-info .well').html(html);
		$('.step.result-info').data('result', result);
		$('.step.result-info').trigger('result.shown');
		$('.results-search').hide();
	}
	private static searchForSubmitListener():void{
		Crawler.$form.on('submit', function(e){
			Crawler.goToStep('results');
			const type = $('input[name=type]', this).val();
			e.preventDefault();
			$(this).formAjax({
				success: (data: webuilder.AjaxResponse) => {
					let results:Result[];
					switch(type){
						case('1'):
							results = Crawler.sources.artist.search(data);
							break;
						case('3'):
							results = Crawler.sources.track.search(data);
							break;
					}
					Crawler.showResultForSearch(results);
					$('.results-search').show();
				},
				error: function(error:webuilder.AjaxError){
					if(error.error == 'data_duplicate' || error.error == 'data_validation'){
						let $input = $('[name='+error.input+']');
						let $params = {
							title: 'خطا',
							message:''
						};
						if(error.error == 'data_validation'){
							$params.message = 'داده وارد شده معتبر نیست';
						}
						if($input.length){
							$input.inputMsg($params);
						}else{
							$.growl.error($params);
						}
					}else{
						$.growl.error({
							title:"خطا",
							message:'درخواست شما توسط سرور قبول نشد'
						});
					}
				}
			});
		});
	}
	public static init(){
		let $body = $('body');
		Crawler.sources = {
			artist: new artist(),
			album: new album(),
			track: new track()
		};
		Crawler.$form = $('.crawler-add-form');
		Crawler.typeListener();
		Crawler.searchForSubmitListener();
	}
	public static initIfNeeded(){
		if($('body').hasClass('crawler-add')){
			Crawler.init();
		}
	}
}