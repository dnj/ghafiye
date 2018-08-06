import * as $ from "jquery";
import AutoComplete from "../../classes/AutoComplete";

export default class Search {
	public static initIfNeeded() {
		if ($("body").hasClass("contributes-search")) {
			Search.init();
		}
	}
	protected static init() {
		if ($("#ContributesSearchForm input[name=song]").length) {
			Search.runSongsAutoComplete();
		}
		if ($("#ContributesSearchForm input[name=user]").length) {
			Search.runUsersAutoComplete();
		}
	}
	protected static runSongsAutoComplete() {
		const $ac = new AutoComplete($("#ContributesSearchForm input[name=song_name]"));
		$ac.songs();
	}
	protected static runUsersAutoComplete() {
		const $ac = new AutoComplete($("#ContributesSearchForm input[name=user_name]"));
		$ac.users();
	}
}
