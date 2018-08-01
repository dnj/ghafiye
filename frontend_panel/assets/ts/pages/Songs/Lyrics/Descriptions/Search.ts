import AutoComplete from "../../../../classes/AutoComplete";

export default class Search {
	public static initIfNeeded() {
		if ($("body").hasClass("songs-lyrics")) {
			Search.init();
		}
	}
	protected static init() {
		Search.runSongsAutoComplete();
		if ($("#DescriptionsSearchForm input[name=user_name]").length) {
			Search.runUsersAutoComplete();
		}
	}
	protected static runSongsAutoComplete() {
		let ac = new AutoComplete("#DescriptionsSearchForm input[name=song_name]");
		ac.songs();
	}
	protected static runUsersAutoComplete() {
		let ac = new AutoComplete("#DescriptionsSearchForm input[name=user_name]");
		ac.users();
	}
}
