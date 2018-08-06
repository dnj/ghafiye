import * as $ from "jquery";
import Search from "./Contributes/Search";

export default class Contributes {
	public static initIfNeeded() {
		if ($("body").hasClass("contributes")) {
			Search.initIfNeeded();
		}
	}
}
