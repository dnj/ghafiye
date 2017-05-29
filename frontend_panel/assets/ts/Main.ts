import * as $ from "jquery";
import Group from "./pages/Group";
import Album from "./pages/Album";
import Genre from "./pages/Genre";
import Person from "./pages/Person";
import Song from "./pages/Song";
$(function(){
	Album.initIfNeeded();
	Group.initIfNeeded();
	Genre.initIfNeeded();
	Person.initIfNeeded();
	Song.initIfNeeded();
});