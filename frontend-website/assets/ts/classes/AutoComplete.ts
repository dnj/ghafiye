import * as $ from "jquery";
import "jquery-ui/ui/widgets/autocomplete";
import { AjaxRequest } from "webuilder";

export default class AutoComplete {
	protected $element: JQuery;
	protected $input: JQuery;
	public constructor($element: JQuery | string) {
		if(typeof $element == 'string') {
			$element = $($element);
		}
		let input = $element.attr('name');
		input = input.substring(0, input.lastIndexOf('_'));
		this.$element = $element;
		this.$input = $element.parents('form').find(`input[name='${input}']`);
	}
	public persons() {
		this.runAutocomplete("contribute/person/search", function( ul:any, item:any ) {
			let html = "<strong>" +item.name+ "</strong>";
			if (item.status === 2) {
				html += "<small>این شخص توسط شما اضافه شده و هنوز تایید نشده است</small>";
			}
			return $( "<li>" )
				.append( html )
				.appendTo( ul );
		}, (event, ui) => {
			this.$element.val(ui.item.name);
			this.$input.val(ui.item.id).trigger('change');
			return false;
		});
	}
	public albums() {
		this.runAutocomplete("userpanel/albums", function( ul:any, item:any ) {
			return $( "<li>" )
				.append( "<strong>" +item.title+ "</strong>" )
				.appendTo( ul );
		}, (event, ui) => {
			this.$element.val(ui.item.title);
			this.$input.val(ui.item.id).trigger('change');
			return false;
		});
	}
	public groups() {
		this.runAutocomplete("contribute/group/search", function( ul:any, item:any ) {
			let html = "<strong>" +item.name+ "</strong>";
			if (item.status === 2) {
				html += "<small>این گروه توسط شما اضافه شده و هنوز تایید نشده است</small>";
			}
			return $( "<li>" )
				.append( html )
				.appendTo( ul );
		}, (event, ui) => {
			this.$element.val(ui.item.name);
			this.$input.val(ui.item.id).trigger('change');
			return false;
		});
	}
	protected runAutocomplete(url:string, render:(ul:any,item:any)=>void, select:JQueryUI.AutocompleteEvent) {
		this.$element.autocomplete({
			source: function( request:any, response:any ) {
				AjaxRequest({
					url: url,
					data: {
						word: request.term
					},
					success: function( data ) {
						response( data.items );
					}
				});
			},
			select: select,
			focus: select,
			create: function() {
				 $(this).data('ui-autocomplete')._renderItem  = render;
			}
		});
	}
}
