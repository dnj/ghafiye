import Add from  "./Songs/Add";
import Translate from "./Songs/Translate";
import Sync from "./Songs/Sync";
import Edit from "./Songs/Edit";

export default class Songs {
	public static initIfNeeded() {
		Add.initIfNeeded();
		Translate.initIfNeeded();
		Sync.initIfNeeded();
		Edit.initIfNeeded();
	}
}
