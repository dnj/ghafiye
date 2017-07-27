export class paginate{
	private currentPage:number;
	private totalItems:number;
	private totalPages:number;
	private itemsPage:number;
	public setCurrentPage(currentPage:number):void{
		this.currentPage = currentPage;
	}
	public setTotalItems(totalItems:number):void{
		this.totalItems = totalItems;
	}
	public setTotalPages(totalPages:number):void{
		this.totalPages = totalPages;
	}
	public setItemsPage(itemsPage:number):void{
		this.itemsPage = itemsPage;
	}
	public getCurrentPage():number{
		return this.currentPage;
	}
	public getTotalItems():number{
		return this.totalItems;
	}
	public getTotalPages():number{
		return this.totalPages;
	}
	public getItemsPage():number{
		return this.itemsPage;
	}
	public paginator($selectbox = false, $mid_range = 7){
		let $return = `<ol class="pagination text-center pull-left hidden-xs">`;

		let prev_page = this.currentPage-1;
		let next_page = this.currentPage+1;

		if(this.currentPage != 1 && this.totalItems >= 10){
			$return += `<li class="prev"><a href="#" data-page="${prev_page}">قبلی</a></li>`;
		}else{
			$return += `<li class="prev disabled"><a>قبلی</a></li>`;
		}
		let start_range = this.currentPage - Math.floor($mid_range/2);
		let end_range = this.currentPage + Math.floor($mid_range/2);

		if(start_range <= 0){
			end_range += Math.abs(start_range)+1;
			start_range = 1;
		}

		if(end_range > this.totalPages){
			start_range -= end_range-this.totalPages;
			end_range = this.totalPages;
		}

		const range = this.range(start_range, end_range);

		for(let i = 1;i <= this.totalPages; i++){
			if(range[0] > 2 && i == range[0]){
				$return += "<li><a> ... </a></li>";
			}
			// loop through all pages. if first, last, or in range, display
			if(i == 1 || i == this.totalPages || this.in_array(i, range)){
				if(i == this.currentPage){
					$return += `<li class="active"><a href="#">${i}</a></li>`;
				}else{
					$return += `<li><a href="#" data-page="${i}">${i}</a></li>`;
				}
			}
			if(range[$mid_range - 1] < this.totalPages - 1 && i == range[$mid_range - 1]){
				$return += "<li><a> ... </a></li>";
			}
		}
		if(this.currentPage != this.totalPages && this.totalItems >= 10){
			$return += `<li class="next"><a href="#" data-page="${next_page}">بعدی</a></li>`;
		}else{
			$return += `<li class="next disabled"><a>بعدی</a></li>`;
		}
		$return += "</ol>";
		$return += `<div class="visible-xs">`;
		$return += `<span class="paginate">صفحه: </span>`;
		$return += `<select class="paginate">`;
		for(let i = 1;i <= this.totalPages;i++){
			$return += `<option value="${i}" data-page="${i}"${(i == this.currentPage ? ' selected' : '')}>${i}</option>`;
		}
		$return += "</select>";
		let $paging = $('.main-content .container .step.search.paging');
		if(!$paging.length){
			$paging = $('<div class="step search paging active"></div>').appendTo($('.main-content .container'))
		}
		$paging.html($return);
	}
	public range(min:number, max:number):number[]{
		let range:number[] = [];
		for(let i = min; i <= max; i++){
			range.push(i);
		}
		return range;
	}
	public in_array(value:any, array:any):boolean{
		for(const part of array){
			if(value == part){
				return true;
			}
		}
		return false;
	}
}