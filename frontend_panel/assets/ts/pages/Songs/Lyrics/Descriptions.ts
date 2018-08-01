import Search from "./Descriptions/Search";
import Edit from "./Descriptions/Edit";

export default class Descriptions {
	public static initIfNeeded() {
		Search.initIfNeeded();
		Edit.initIfNeeded();
	}	
}
