import * as $ from "jquery";
import { AjaxRequest } from "webuilder";
import Songs from "./Contributes/Songs";

interface IContribute {
	title: string;
	done_at: string;
	user: {
		name: string;
		avatar: string;
	};
	song: {
		title: string;
		avatar: string;
		url: string;
		singer: {
			title: string;
			url: string;
		}
	}
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
	protected static appendToContributes(item: IContribute) {
		const html = `<div class="row">
			<div class="col-xs-12">
				<div class="contribute-info">
					<time>${item.done_at}</time>
					<div class="row">
						<div class="col-sm-1 col-xs-2">
							<div class="contributor-avatar">
								<img src="${item.user.avatar}" class="img-responsive img-circle">
							</div>
						</div>
						<div class="col-sm-11 col-xs-10">
							<div class="contributor-name">
								<p>${item.user.name}</p>
								<span>${item.title}</span>
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
											<img src="${item.song.avatar}" alt="${item.song.title}">
										</div>
										<div class="col-sm-10 col-xs-9">
											<p><a target="_blank" href="${item.song.url}">${item.song.title}</a></p>
											<p><a target="_blank" href="${item.song.singer.url}" class="song-singer">${item.song.singer.title}</a></p>
										</div>
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
