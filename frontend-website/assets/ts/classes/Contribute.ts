import * as $ from "jquery";
import { AjaxRequest, Router } from "webuilder";

interface ITrack {
	id: number;
	image: string;
	song: {
		title: string;
		url: string;
	};
	singer: {
		name: string;
		url: string;
	};
}

export default class Contribute {
	public static initIfNeeded() {
		if ($("body").hasClass("contribute-main") || $("body").hasClass("contribute-synce") || $("body").hasClass("contribute-translate")) {
			Contribute.init();
		}
	}
	public static getTranslateTrackHtml(track: ITrack): string {
		return `<li class="list-group-item">
			<div class="row">
				<div class="col-sm-1 col-xs-2 track-img">
					<a href="${track.song.url}">
						<img src="${track.image}" alt="${track.song.title}">
					</a>
				</div>
				<div class="col-sm-8 col-xs-7">
					<a class="track-title" href="${track.song.url}">${track.song.title}</a>
					<a class="track-singer-name" href="${track.singer.url}">${track.singer.name}</a>
				</div>
				<div class="col-sm-3 col-xs-3">
					<a href="${Router.url("songs/translate/" + track.id)}" class="btn btn-sm btn-block btn-default btn-translate"><span class="hidden-xs">ترجمه کنید</span><span class="visible-xs"><i class="fa fa-edit"></i></span></a>
				</div>
			</div>
		</li>`;
	}
	public static getSynceTrackHtml(track: ITrack): string {
		return `<li class="list-group-item">
			<div class="row">
				<div class="col-sm-1 col-xs-2 track-img">
					<a href="${track.song.url}">
						<img src="/packages/ghafiye/storage/public/resized/cover_placeholder_32x32.png" alt="${track.song.title}">
					</a>
				</div>
				<div class="col-sm-8 col-xs-7">
					<a class="track-title" href="${track.song.url}">${track.song.title}</a>
					<a class="track-singer-name" href="${track.singer.url}">${track.singer.name}</a>
				</div>
				<div class="col-sm-3 col-xs-3">
					<a href="${Router.url("songs/synce/" + track.id)}" class="btn btn-sm btn-block btn-default btn-synce"><span class="hidden-xs">همگام سازی</span><span class="visible-xs"><i class="fa fa-check-square-o"></i></span></a>
				</div>
			</div>
		</li>`;
	}
	protected static $tranlatePanel: JQuery;
	protected static $syncePanel: JQuery;
	protected static init() {
		Contribute.$tranlatePanel = $(".panel-translate");
		Contribute.$syncePanel = $(".panel-synce");
		Contribute.loadMoreTrackListener();
	}
	protected static loadMoreTrackListener() {
		let translatePage = 1;
		$(".panel-footer .btn-more-translate-track", Contribute.$tranlatePanel).on("click", function(e) {
			e.preventDefault();
			if ($(this).hasClass("disabled")) {
				return;
			}
			$(".panel-icon i", Contribute.$tranlatePanel).removeClass("fa-globe").addClass("fa-spinner fa-pulse fa-fw");
			$(this).addClass("disabled");
			const that = this;
			AjaxRequest({
				url: "contribute/translate?ajax=1",
				dataType: "json",
				data: {
					ipp: 7,
					page: ++translatePage,
				},
				success: function(data) {
					$(".panel-icon i", Contribute.$tranlatePanel).removeClass("fa-spinner fa-pulse fa-fw").addClass("fa-globe");
					if ($(".panel-body .tracks.list-group li", Contribute.$tranlatePanel).get().length === data.total_items) {
						return;
					}
					for (const track of data.items as ITrack[]) {
						$(".panel-body .tracks.list-group", Contribute.$tranlatePanel).append(Contribute.getTranslateTrackHtml(track));;
					}
					$(that).removeClass("disabled");
				},
				error: function() {
					$.growl.error({
						title: "خطا",
						message: "سرور پاسخ درخواست شما را به درستی ارسال نمی کند"
					});
					$(".panel-icon i", Contribute.$tranlatePanel).removeClass("fa-spinner fa-pulse fa-fw").addClass("fa-globe");
					$(that).removeClass("disabled");
				}
			});
		});
		let syncePage = 1;
		$(".panel-footer .btn-more-sync-track", Contribute.$syncePanel).on("click", function(e) {
			e.preventDefault();
			if ($(this).hasClass("disabled")) {
				return;
			}
			$(".panel-icon i", Contribute.$syncePanel).removeClass("fa-clock-o").addClass("fa-spinner fa-pulse fa-fw");
			$(this).addClass("disabled");
			const that = this;
			AjaxRequest({
				url: "contribute/synce?ajax=1",
				dataType: "json",
				data: {
					ipp: 7,
					page: ++syncePage,
				},
				success: function(data) {
					$(".panel-icon i", Contribute.$syncePanel).removeClass("fa-spinner fa-pulse fa-fw").addClass("fa-clock-o");
					if ($(".panel-body .tracks.list-group li", Contribute.$syncePanel).get().length === data.total_items) {
						return;
					}
					for (const track of data.items as ITrack[]) {
						$(".panel-body .tracks.list-group", Contribute.$syncePanel).append(Contribute.getSynceTrackHtml(track));;
					}
					$(that).removeClass("disabled");
				},
				error: function() {
					$.growl.error({
						title: "خطا",
						message: "سرور پاسخ درخواست شما را به درستی ارسال نمی کند"
					});
					$(".panel-icon i", Contribute.$syncePanel).removeClass("fa-spinner fa-pulse fa-fw").addClass("fa-clock-o");
					$(that).removeClass("disabled");
				}
			});
		});
	}
}
