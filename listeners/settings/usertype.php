<?php
namespace packages\ghafiye\listeners\settings;
use \packages\userpanel\usertype\permissions;
class usertype{
	public function permissions_list(){
		$permissions = array(
			'persons_list',
			'person_add',
			'person_edit',
			'person_delete',
			'person_name_add',
			'person_name_delete',

			'groups_list',
			'group_add',
			'group_edit',
			'group_delete',

			'albums_list',
			'album_add',
			'album_edit',
			'album_delete',

			'songs_list',
			'song_add',
			'song_edit',
			'song_delete',

			'genres_list',
			'genre_add',
			'genre_edit',
			'genre_delete',

			'crawler_search',
			'crawler_add',
			'crawler_delete',

			"songs_comments_search",
			"songs_comments_view",
			"songs_comments_edit",
			"songs_comments_delete",

			"songs_lyrics_descriptions_search",
			"songs_lyrics_descriptions_edit",
			"songs_lyrics_descriptions_delete",

			"contributes_search",
			"contributes_view",
			"contributes_edit",
			"contributes_delete",
			
			"can_login_in_userpanel",
		);
		foreach($permissions as $permission){
			permissions::add('ghafiye_'.$permission);
		}
	}
}
