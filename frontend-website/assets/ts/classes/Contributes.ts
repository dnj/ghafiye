import * as $ from "jquery";
import { AjaxRequest, Router } from "webuilder";
import Songs from "./Contributes/Songs";

interface IContribute {
	id: number;
	title: string;
	done_at: string;
	image: string;
	user: {
		id: number;
		name: string;
		avatar: string;
	};
	song?: {
		title: string;
		url: string;
		singer: {
			title: string;
			url: string;
		}
	};
	person?: {
		name: string;
		url: string;
	};
	group?: {
		name: string;
		url: string;
	};
	album?: {
		name: string;
		url: string;
	};
}

export default class Contributes {
	public static initIfNeeded() {
		Songs.initIfNeeded();
		if ($("body").hasClass("contributes")) {
			Contributes.init();
		}
	}
	public static isLtr(lang: string): boolean {
		let ltrLangs = ["ar", "fa", "dv", "he", "ps", "sd", "ur", "yi", "ug", "ku"];
		for (let ltrLang of ltrLangs) {
			if (ltrLang === lang) {
				return false;
			}
		}
		return true;
}
	protected static init() {
		Contributes.listenForMoreActivities();
	}
	protected static listenForMoreActivities() {
		let page = 1;
		$(".contributes .btn-load-more").on("click", function() {
			$(this).prop("disabled", true);
			$(this).prepend(`<i class="fa fa-spinner fa-pulse fa-fw"></i>`);
			const that = this;
			AjaxRequest({
				url: window.location.href,
				dataType: "json",
				data: {
					page: ++page,
				},
				success: function(data) {
					$(that).prop("disabled", false);
					$(".fa", that).remove();
					if (!data.items.length || $(".contributes .contribute-info").get().length === data.total_items) {
						$(that).remove();
						return;
					}
					for (const item of data.items as IContribute[]) {
						Contributes.appendToContributes(item);
					}
					$(".contributes").append($(that).detach());
				},
				error: function() {
					$(that).prop("disabled", false);
					$(".fa", that).remove();
					$.growl.error({
						title: 'خطا',
						message: " در حال حاضر سرور پاسخ درخواست شما را به درستی ارسال نمیکند."
					});
				}
			})
		});
	}
	protected static appendToContributes(contribute: IContribute) {
		let html = `<div class="row">
			<div class="col-xs-12">
				<div class="contribute-info">
					<time>${contribute.done_at}</time>
					<div class="row">
						<div class="col-sm-1 col-xs-2">
							<div class="contributor-avatar">
								<a href="${Router.url("profile/" + contribute.user.id)}">
									<img src="${contribute.user.avatar}" class="img-responsive img-circle">
								</a>
							</div>
						</div>
						<div class="col-sm-11 col-xs-10">
							<div class="contributor-name">
								<a href="${Router.url("profile/" + contribute.user.id)}">${contribute.user.name}</a>
								<a class="link-muted" href="${Router.url("contribute" + contribute.id)}">${contribute.title}</a>
							</div>
						</div>
					</div>
				</div>
				<div class="contribute-container">
					<div class="row">
						<div class="col-sm-11 col-sm-offset-1">
							<div class="panel panel-default">
								<div class="panel-body">
									<div class="row">
										<div class="col-sm-2 col-xs-3">
											<img src="${contribute.image}" alt="${contribute.title}">
										</div>
										<div class="col-sm-10 col-xs-9">`;
											if (contribute.hasOwnProperty("song")) {
												html +=	`<a target="_blank" href="${contribute.song.url}">${contribute.song.title}</a>
												<a target="_blank" href="${contribute.song.singer.url}" class="song-singer">${contribute.song.singer.title}</a>`;
											} else if (contribute.hasOwnProperty("person")) {
												html += `<a href="${contribute.person.url}">${contribute.person.name}</a>`;
											} else if (contribute.hasOwnProperty("group")) {
												html += `<a href="${contribute.group.url}">${contribute.group.name}</a>`;
											} else if (contribute.hasOwnProperty("album")) {
												html += `<a href="${contribute.album.url}">${contribute.album.name}</a>`;
											}
								html += `</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>`;
		$(".contributes").append(html);
	}
}
