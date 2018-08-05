<?php
namespace themes\musixmatch\views\lyrics;
use \packages\base;
use \packages\base\{db, translator, frontend\theme, date};
use \packages\ghafiye\{genre, album, song, song\person, views\lyrics\view as lyricsView};
use \themes\musixmatch\{viewTrait, views\formTrait};

class view extends lyricsView {
	use viewTrait, formTrait;
	protected $song;
	protected $singer;
	protected $loadSideAd = false;
	protected $comments;
	function __beforeLoad() {
		$this->song = $this->getSong();
		$this->singer = $this->song->getPerson(person::singer);

		$this->setTitle(array(
			$this->singer->name($this->getLyricsLanguage()),
			$this->song->title($this->getLyricsLanguage())
		));
		$this->addBodyClass('article');
		$this->addBodyClass('lyric');
		$this->addMetaTags();
		$this->comments = $this->getComments();
	}
	function getGenres() {
		return genre::getActives(6);
	}
	protected function getTopSongs() {
		$song = new song();
		$song->where("status", song::publish);
		$song->orderBy("views", "desc");
		$song->setQueryOption("DISTINCT");
		return $song->get(6);
	}
	protected function getTopSongsByGenre(genre $genre) {
		$song = new song();
		$song->where("status", song::publish);
		$song->where("genre", $genre->id);
		$song->orderBy("views", "desc");
		$song->setQueryOption("DISTINCT");
		return $song->get(6);
	}
	protected function numberOfLangs() {
		db::where("song", $this->song->id);
		return db::getValue("ghafiye_songs_lyrices", "count(DISTINCT `lang`)");
	}
	protected function getLangs() {
		db::where("song", $this->song->id);
		db::where('lang', [$this->song->lang, 'fa'], 'not in');
		return array_column(db::get("ghafiye_songs_titles",null, "ghafiye_songs_titles.*"), 'lang');
	}
	protected function isLang(string $language):bool{
		db::where("song", $this->song->id);
		db::where('lang', $language);
		return db::has("ghafiye_songs_titles");
	}
	protected function is_ltr($lang) {
		return !in_array($lang, array('ar','fa','dv','he','ps','sd','ur','yi','ug','ku'));
	}
	protected function getSongs() {
		if ($album = $this->song->album) {
			$song = new song();
			$song->where("ghafiye_songs.status", song::publish);
			$song->where('ghafiye_songs.id', $this->song->id, '!=');
			$song->where('ghafiye_songs.album', $album->id);
			$song->orderBy("ghafiye_songs.release_at", "desc");
			$song->setQueryOption("DISTINCT");
			return $song->get(4, 'ghafiye_songs.*');
		}
	}
	protected function isMoreSong():bool{
		$album = $this->song->album;
		$song = new song();
		$song->where("ghafiye_songs.status", song::publish);
		$song->where('ghafiye_songs.id', $this->song->id, '!=');
		$song->where('ghafiye_songs.album', $album->id);
		return $song->count() > 4;
	}
	protected function getShareSocial() {
		$lang = $this->getLyricsLanguage();
		$singer = $this->song->getPerson(person::singer);
		$url = base\url($this->singer->encodedName().'/'.$this->song->encodedTitle(), [], true);
		return [
			[
				"name"=> "facebook",
				"link"=> "http://www.facebook.com/sharer.php?u=".$url
			],
			[
				"name"=> "telegram",
				"link"=> "tg://msg_url?text=".translator::trans('share.song.on.telegram.text', ['title'=>$this->song->title($lang), 'artist'=>$this->singer->name($lang)])."&url=".$url
			],
			[
				"name"=> "twitter",
				"link"=> "http://www.twitter.com/share?url=".$url
			],
			[
				"name"=> "google-plus",
				"link"=> "http://www.plus.google.com/share?url=".$url
			],
			[
				"name"=> "pinterest",
				"link"=> "http://www.pinterest.com/pin/create/button/?url=".$url
			],
			[
				"name"=> "linkedin",
				"link"=> "https://www.linkedin.com/cws/share?url=".$url
			],
			[
				"name"=> "tumblr",
				"link"=> "http://www.tumblr.com/share/link?url=".$url
			],
			[
				"name"=> "vk",
				"link"=> "http://www.vk.com/share.php?url=".$url
			],
			[
				"name"=> "reddit",
				"link"=> "http://www.reddit.com/submit?url=".$url
			],
			[
				"name"=> "mail",
				"link"=> "mailto:?subject={$this->song->title($lang)}&body={$this->song->title($lang)}\n".$url
			]
		];
	}
	protected function getOrginalLyrices():array{
		$orginalLyrics = [];
		foreach($this->getLyrices() as $lyric) {
			if (!$lyric->parent) {
				$orginalLyrics[] = $lyric;
			}
		}
		return $orginalLyrics;
	}
	protected function getTranslateLyricById(int $parent) {
		static $parents;
		if (!$parents) {
			$parents = array();
			foreach($this->getLyrices() as $key => $lyric) {
				$parents[$key] = $lyric->parent;
			}
		}
		$lang = $this->getLyricsLanguage();
		$translate_index = array_search($parent, $parents, true);
		if ($translate_index !== false) {
			return $this->getLyrices()[$translate_index];
		}
		return null;
	}
	protected function getAlbumReleaseDate(album $album) {
		db::where("album", $album->id);
		db::where("status", song::publish);
		db::orderby('release_at', 'desc');
		return db::getValue('ghafiye_songs', 'release_at');
	}
	protected function getAlbums():array{
		if ($this->song->album === null) {
			return [];
		}
		if (!$this->song->group) {
			return album::where("ghafiye_songs.status", song::publish)->where('ghafiye_albums.id', $this->song->album->id, '!=')->bySinger($this->singer, 4);
		} else {
			return album::where("ghafiye_songs.status", song::publish)->where('ghafiye_albums.id', $this->song->album->id, '!=')->byGroup($this->song->group, 4);
		}
	}
	protected function isMoreAlbum():bool{
		if (!$this->song->group) {
			return count(album::where("ghafiye_songs.status", song::publish)->where('ghafiye_albums.id', $this->song->album->id, '!=')->bySinger($this->singer)) > 4;
		} else {
			return count(album::where("ghafiye_songs.status", song::publish)->where('ghafiye_albums.id', $this->song->album->id, '!=')->byGroup($this->song->group)) > 4;
		}
	}
	protected function getPopularSongs():array{
		db::setQueryOption("DISTINCT");
		if (!$this->song->group) {
			return song::where("status", song::publish)->orderBy("views", "DESC")->bySinger($this->singer, 5);
		} else {
			return song::where('ghafiye_songs.group', $this->song->group->id)->orderBy("views", "DESC")->get(5, "ghafiye_songs.*");
		}
	}
	protected function isMorePopularSong():bool{
		db::setQueryOption("DISTINCT");
		if (!$this->song->group) {
			return count(song::where("status", song::publish)->bySinger($this->singer)) > 5;
		} else {
			return song::where('ghafiye_songs.group', $this->song->group->id)->where('ghafiye_songs.id', $this->song->id, "!=")->count() > 5;
		}
	}
	private function addMetaTags() {
		$lang = $this->getLyricsLanguage();
		$this->addMetaTag(array(
			'property' => 'og:title',
			'content' => translator::trans('ghafiye.lyric.view.metaTag.og:title', ['title'=>$this->song->title($lang), 'artist'=>$this->singer->name($lang)])
		));
		if ($this->song->lang == $lang) {
			$this->addMetaTag(array(
				'property' => 'og:description',
				'content' => translator::trans('ghafiye.lyric.view.metaTag.description', ['title'=>$this->song->title($lang), 'artist'=>$this->singer->name($lang)])
			));
		} else {
			$this->addMetaTag(array(
				'property' => 'og:description',
				'content' => translator::trans('ghafiye.lyric.view.metaTag.description.translated', ['title'=>$this->song->title($lang), 'artist'=>$this->singer->name($lang)])
			));
		}
		$this->addMetaTag(array(
			'property' => 'og:type',
			'content' => 'song.lyric'
		));
		$this->addMetaTag(array(
			'property' => 'og:image',
			'content' => $this->song->getImage(255, 255, 'image', true)
		));
		$this->addMetaTag(array(
			'property' => 'song:artist:name',
			'content' => $this->singer->name($lang)
		));
		$this->addMetaTag(array(
			'name' => 'twitter:card',
			'content' => 'summary_large_image'
		));
		$this->addMetaTag(array(
			'property' => 'twitter:title',
			'content' => translator::trans('ghafiye.lyric.view.metaTag.og:title', ['title'=>$this->song->title($lang), 'artist'=>$this->singer->name($lang)])
		));
		if ($this->song->lang == $lang) {
			$this->addMetaTag(array(
				'property' => 'twitter:description',
				'content' => translator::trans('ghafiye.lyric.view.metaTag.description', ['title'=>$this->song->title($lang), 'artist'=>$this->singer->name($lang)])
			));
		} else {
			$this->addMetaTag(array(
				'property' => 'twitter:description',
				'content' => translator::trans('ghafiye.lyric.view.metaTag.description.translated', ['title'=>$this->song->title($lang), 'artist'=>$this->singer->name($lang)])
			));
		}
	}
	protected function getTags():array{
		$lang = $this->getLyricsLanguage();
		if ($this->song->lang == $lang) {
			$tags = [
				[
					'url' => base\url("{$this->singer->encodedName($lang)}/{$this->song->encodedTitle($lang)}"),
					'content' => translator::trans('ghafiye.song.lyric.tag', [
						'name'=>$this->singer->name($lang),
						'title' => $this->song->title()
					])
				],
				[
					'url' => base\url("{$this->singer->encodedName($lang)}/{$this->song->encodedTitle($lang)}"),
					'content' => translator::trans('ghafiye.song.tag', [
						'name'=>$this->singer->name(),
						'title' => $this->song->title()
					])
				]
			];
			if ($this->song->album) {
				$tags[] = [
					'url' => base\url("{$this->singer->encodedName($lang)}/albums/{$this->song->album->title()}"),
					'content' => translator::trans('ghafiye.song.album.tag', [
						'name'=> $this->song->album->title(),
						'title' => $this->song->title()
					])
				];
				$tags[] = [
					'url' => base\url("{$this->singer->encodedName($lang)}/albums/{$this->song->album->title()}"),
					'content' => translator::trans('ghafiye.song.album.lyric.tag', [
						'name'=> $this->song->album->title(),
						'title' => $this->song->title()
					])
				];
			}
		} else {
			$tags = [
				[
					'url' => base\url("{$this->singer->encodedName($lang)}/{$this->song->encodedTitle($lang)}"),
					'content' => translator::trans('ghafiye.song.lyric.tag.translated', [
						'name'=> $this->singer->name($this->song->lang),
						'title' => $this->song->title($lang)
					])
				],
				[
					'url' => base\url("{$this->singer->encodedName($lang)}/{$this->song->encodedTitle($lang)}"),
					'content' => translator::trans('ghafiye.song.tag.translated', [
						'name'=> $this->singer->name(),
						'title' => $this->song->title()
					])
				],
				[
					'url' => base\url("{$this->singer->encodedName($lang)}/{$this->song->encodedTitle($lang)}"),
					'content' => translator::trans('ghafiye.songs.lyric.tag.translated', [
						'name'=>$this->singer->name(),
					])
				]
			];
			if ($this->song->album) {
				$tags[] = [
					'url' => base\url("{$this->singer->encodedName($lang)}/albums/{$this->song->album->title()}"),
					'content' => translator::trans('ghafiye.song.album.tag.translated', [
						'name'=> $this->song->album->title(),
						'title' => $this->song->title()
					])
				];
				$tags[] = [
					'url' => base\url("{$this->singer->encodedName($lang)}/albums/{$this->song->album->title()}"),
					'content' => translator::trans('ghafiye.song.album.lyric.tag.translated', [
						'name'=> $this->song->album->title(),
						'title' => $this->song->title()
					])
				];
			}
		}
		return $tags;
	}
	protected function getSideAd() {
		if ($this->loadSideAd) {
			return "";
		}
		$this->loadSideAd = true;
		return '<div class="row">
			<div class="col-xs-12">
				<a class="banner-ad" href="https://www.jeyserver.com" target="_blank" title="هاست لینوکس، برنامه نویسی php">
					<img src="' . theme::url("assets/images/ads/3078323516528828115111121661694.gif") . '" alt="جی هاست لینوکس، برنامه نویسی php">
					</a>
				</div>
			</div>';
	}
	protected function getTopAd() {
		/*return '<div class="row">
		<div class="col-xs-12">
			<a class="banner-ad banner-top" href="https://www.jeyserver.com" target="_blank" title="هاست لینوکس، برنامه نویسی php">
				<img src="' . theme::url("assets/images/ads/3078323516528828115111129128503.gif") . '" alt="جی هاست لینوکس، برنامه نویسی php">
				</a>
			</div>
		</div>';*/
		return "";
	}
	protected function getComments() {
		$comment = new song\Comment();
		$comment->where("song", $this->song->id);
		$comment->where("status", song\Comment::accepted);
		$comment->orderBy("sent_at", "DESC");
		return $comment->get();
	}
	protected function revertReply($reply = null): string {
		$html = "";
		if (!$this->comments) {
			return $html;
		}
		$replyText = translator::trans("blog.post.comment.reply");
		foreach ($this->comments as $comment) {
			if ($comment->reply == $reply) {
				$html .= '<div id="comment-' . $comment->id .'" class="row comment' . ($comment->reply ? " reply" : "") . '">';
					$html .= '<div class="col-sm-1 col-xs-2">';
						$gravatar = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($comment->email))). "?s=50&r=G&d=" . urlencode("http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=50");
						$html .= '<img src="' . $gravatar . '" alt="' . $comment->name . '" class="img-circle">';
					$html .= '</div>';
					$html .= '<div class="col-sm-11 col-xs-10">';
						$html .= '<div class="panel panel-default">';
							$html .= '<div class="panel-heading">';
								$html .= '<div class="row">';
									$html .= '<div class="col-sm-8 col-xs-10"><p class="comment-sender-name">' . $comment->name . ' ' . ($comment->reply ? translator::trans("ghafiye.comment.replyTo") . ' <a href="#comment-' . $comment->reply . '"><strong>' . $comment->getReply()->name . "</strong></a>": "") . '</p><p class="visible-xs"><time class="tooltips" title="' . date::format("Y/m/d H:i", $comment->sent_at) . '">' . date::relativeTime($comment->sent_at) . '</time></p></div>';
									$html .= '<div class="col-sm-3 comment-time hidden-xs">';
										$html .= '<time class="tooltips" title="' . date::format("Y/m/d H:i", $comment->sent_at) . '">';
										$html .= date::relativeTime($comment->sent_at);
										$html .= '</time>';
									$html .= '</div>';
									$html .= '<div class="col-sm-1 col-xs-2 text-left">';
										$html .= '<button type="button" class="btn btn-xs btn-link btn-reply tooltips" data-comment="' . $comment->id . '" data-sender-name="' . $comment->name .'" title="' . translator::trans("ghafiye.reply.comment") . '"><i class="fa fa-reply"></i></button>';
									$html .= '</div>';
								$html .= '</div>';
							$html .= '</div>';
							$html .= '<div class="panel-body">';
								$html .= nl2br($comment->content);
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';
				$html .= '</div>';
				$html .= $this->revertReply($comment->id);
			}
		}
		return $html;
	}
}
