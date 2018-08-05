import Add from  "./Songs/Add";
import Translate from "./Songs/Translate";

export default class Songs {
	public static initIfNeeded() {
		Add.initIfNeeded();
		Translate.initIfNeeded();
	}
}
