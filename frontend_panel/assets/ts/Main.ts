import * as $ from "jquery";
import Group from "./pages/Group";
import Album from "./pages/Album";
import Genre from "./pages/Genre";
import Person from "./pages/Person";
import Song from "./pages/Song";
import {Crawler} from "./pages/Crawler";
import Contributes from "./pages/Contributes";

$(function(){
	Album.initIfNeeded();
	Group.initIfNeeded();
	Genre.initIfNeeded();
	Person.initIfNeeded();
	Song.initIfNeeded();
	Crawler.initIfNeeded();
	Contributes.initIfNeeded();
});