import Add from  "./Songs/Add";
import Translate from "./Songs/Translate";
import Sync from "./Songs/Sync";

export default class Songs {
	public static initIfNeeded() {
		Add.initIfNeeded();
		Translate.initIfNeeded();
		Sync.initIfNeeded();
	}
}
