import * as $ from "jquery";
import "jquery.growl";
import { Router, AjaxRequest, webuilder } from "webuilder";
import "bootstrap/js/modal";
import "bootstrap/js/popover";
import "jquery-bootstrap-checkbox";
import {artist} from "./crawler/artist.source";
import {album} from "./crawler/album.source";
import {track} from "./crawler/track.source";
import {paginate} from "../classes/paginate";
export interface Result{
	type: "artist" | "track" | "album";
	id:number;
	title:string;
	select(btn:JQuery, html:string):void;
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
				const html = $(this).html();
				$(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
				$(this).prop('disabled', true);
				Crawler.goToStep('import');
				const result = $(this).parents('.card').data('result');
				result.select($(this), html);
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
						<button class="btn btn-lg ${result.canSelect() ? 'btn-select' : ''}" ${result.canSelect() ? '' : 'disabled="disabled"'}><i class="fa fa-plus"></i></button>
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
	public static runPaginate(current_page:number, items_per_page:number, total_items:number, type:string){
		function setEventsForPage(type:string){
			const $pagination = $('.pagination');
			$('li:not(.disabled) a', $pagination).on('click', function(e){
				e.preventDefault();
				const page = $(this).data('page');
				if(!page){
					return;
				}
				$(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
				$(this).prop('disabled', true);
				let data = {};
				const paging = $pagination.parent('.paging');
				switch(type){
					case('1'):
						data = {
							name: paging.data('name'),
							ajax: 1
						};
						Crawler.sources.artist.goTo(page, data);
						break;
					case('3'):
						switch(paging.data('searchBy')){
							case('name'):
								data = {
									name: paging.data('name'),
									type: 3,
									ajax: 1
								};
								break;
							case('artist'):
								const result = paging.data('result');
								data = {
									artist: result.id,
									type: 3,
									ajax: 1
								};
								break;
							case('album'):
								const result = paging.data('result');
								data = {
									album: result.id,
									type: 3,
									ajax: 1
								};
								break;
						}
						Crawler.sources.track.goTo(page, data);
						break;
					case('4'):
						const result = paging.data('result');
						data = {
							artist: result.id,
							type: 4
						};
						Crawler.sources.album.goTo(page, data);
						break;
				}
			});
		}
		const $paginate = new paginate();
		$paginate.setCurrentPage(current_page);
		$paginate.setItemsPage(items_per_page);
		$paginate.setTotalItems(total_items);
		$paginate.setTotalPages(Math.ceil(total_items / items_per_page));
		$paginate.paginator();
		setEventsForPage(type);
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
			const name = $('input[name=name]', this).val();
			const type = $('input[name=type]', this).val() as string;
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
					Crawler.runPaginate(data.current_page, data.items_per_page, data.total_items, type);
					$('.results-search').show();
					$('.container .step.search.paging').data({
						name: name,
						searchBy: 'name'
					});
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