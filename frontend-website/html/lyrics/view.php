<?php
use \packages\base;
use \packages\userpanel\date;
use \packages\base\{translator, frontend\theme};
use \packages\ghafiye\{song\lyric, song\person, person\name as personName, song};
$this->the_header();
$numberOfLangs = $this->numberOfLangs();
$lang = $this->getLyricsLanguage();
if ($this->song->status == song::Block) {
?>
<div class="modal-backdrop fade in filtering-backdrop"></div>
<div class="modal fade in" tabindex="-1" id="filtering-modal" data-show="true" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-ban"></i> فیلترینگ</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-4 text-center">
						<img src="<?php echo theme::url("assets/images/anger.png"); ?>" alt="Anger icon">
					</div>
					<div class="col-sm-8 text-justify">
						<p>ما به تازگی از سوی <a href="http://internet.ir">کمیته مصادیق مجرمانه</a> (فیلترینگ) دستوری مبنی بر مسدود سازی دسترسی کاربران به این آهنگ را دریافت کردیم.</p>
						<p style="font-size:25px;" class="text-center"><strong>شرمنده ایم!</strong><br> که دسترسی به متن آهنگ مورد علاقیتان ندارید.</p>
						<p>صدای ما که به جایی نمیرسد، ولی اگر شما از این مسدود سازی ناراضی هستید لطفا به <a href="http://rafefilter.internet.ir/" target="_blank">این صفحه</a> مراجعه کنید و خواستار رفع فیلتر این آهنگ شوید.</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="https://google.com/search?<?php echo http_build_query(array('q' => 'متن آهنگ ' . $this->song->title() . ' از ' . $this->singer->name())); ?>" class="btn btn-default">جستجو در سایت های دیگر</a>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<header class="row">
	<div class="col-sm-3">
		<img src="<?php echo $this->song->getImage(255, 255); ?>" class="song-image">
	</div>
	<?php $topAd = $this->getTopAd(); ?>
	<div class="col-sm-9 title<?php echo $topAd ? " withAd" : "" ?>">
		<h1><?php echo $this->song->title($lang); ?></h1>
		<h2><a href="<?php echo(base\url($this->singer->encodedName($lang))); ?>"><?php echo $this->singer->name($lang); ?></a></h2>
		<div class="row visible-xs">
			<div class="col-xs-12">
				<?php if ($this->song->synced != song::synced) { ?>
					<a href="<?php echo base\url("contribute/song/sync/" . $this->song->id); ?>" class="btn btn-sync btn-sm btn-block">
						<div class="btn-icon"><i class="fa fa-clock-o"></i></div>
						<?php echo translator::trans("ghafiye.contribute.sync"); ?>
					</a>
				<?php } ?>
			</div>
		</div>
		<div class="row visible-xs">
			<div class="col-xs-12">
					<a href="<?php echo base\url("contribute/song/edit/" . $this->song->id); ?>" class="btn btn-sm btn-edit btn-block">
						<div class="btn-icon"><i class="fa fa-pencil"></i></div>
						<?php echo translator::trans("ghafiye.contribute.edit"); ?>
					</a>
			</div>
		</div>
		<?php
		$lyriclang = $this->getLyricsLanguage();
		$translateUrlParamater = array();
		if ($lyriclang != $this->song->lang) {
			$translateUrlParamater = array("songlang" => $lyriclang);
		}
		?>
		<div class="row visible-xs">
			<div class="col-xs-12">
				<a href="<?php echo base\url("contribute/song/translate/" . $this->song->id, $translateUrlParamater); ?>" class="btn btn-sm btn-translate btn-block">
					<div class="btn-icon"><i class="fa fa-language"></i></div>
					<?php echo translator::trans("ghafiye.contribute.translate"); ?>
				</a>
			</div>
		</div>
		<?php echo $topAd; ?>
		<div class="translations">
			<span><i class="fa fa-language"></i> <?php echo translator::trans('translations'); ?></span>
			<a <?php if($lang == $this->song->lang)echo('class="active"'); ?> href="<?php echo(base\url($this->singer->encodedName($this->song->lang)).'/'.$this->song->encodedTitle($this->song->lang)); ?>"><?php echo translator::trans('translations.langs.original'); ?></a>
			<?php if($this->song->lang != 'fa' and $this->isLang('fa')){ ?>
				<a<?php if($lang == 'fa')echo(' class="active"'); ?> href="<?php echo(base\url($this->singer->encodedName('fa')).'/'.$this->song->encodedTitle('fa')); ?>"><?php echo translator::trans('translations.langs.fa'); ?></a>
			<?php
			}
			if($numberOfLangs < 6){
				foreach(array_reverse($this->getLangs()) as $olang){
					if($this->song->lang != $olang and $this->isLang($olang)){
				?>
						<a<?php if($lang == $olang)echo(' class="active"'); ?> href="<?php echo(base\url($this->singer->encodedName($olang)).'/'.$this->song->encodedTitle($olang)); ?>"><?php echo translator::trans('translations.langs.'.$olang); ?></a>
			<?php
					}
				}
			}else{
			?>
			<select class="selectpicker<?php if($this->is_ltr($lang))echo(' ltr'); ?>"  data-width="fit"  title="<?php echo translator::trans('translations.langs.more', array('number' => $numberOfLangs -2)); ?>">
				<?php
				foreach($this->getLangs() as $tlang){
				?>
				<option value="<?php echo $tlang; ?>" <?php if($this->is_ltr($tlang))echo('class="ltr"'); ?> data-link="<?php echo(base\url($this->singer->encodedName($tlang)).'/'.$this->song->encodedTitle($tlang)); ?>"><?php echo translator::trans('translations.langs.'.$tlang); ?></option>
				<?php } ?>
			</select>
			<?php } ?>
		</div>
	</div>
</header>
<div class="row">
	<div class="col-md-7 col-sm-9 col-md-offset-1 col-md-push-3">
		<section class="text" data-lang="<?php echo $lang; ?>">
			<?php foreach ($this->getOrginalLyrices() as $lyric) { ?>
			<p>
				<?php
				$isLtr = $this->is_ltr($lyric->lang);
				$hasDescription = $lyric->hasDescription();
				?>
				<span class="<?php echo $isLtr ? "ltr" : ""; ?> <?php echo $hasDescription ? "hasdescription" : ""; ?>" data-lyric="<?php echo $lyric->id ?>">
				<?php echo $lyric->text; ?>
				</span>
				<?php
				if ($lang == $this->song->lang) {
					continue;
				}
				$translate = $this->getTranslateLyricById($lyric->id);
				if ($translate) {
					$isLtr = $this->is_ltr($translate->lang);
					$hasDescription = $translate->hasDescription();
				?>
				<span class="<?php echo $isLtr ? 'ltr' : ""; ?> <?php echo $hasDescription ? "hasdescription" : ""; ?>" data-lyric="<?php echo $translate->id ?>">
				<?php echo $translate->text; ?>
				</span>
				<?php } ?>
			</p>
			<?php } ?>
		</section>
		<div class="row">
			<div class="col-sm-12 share-box">
				<h3><?php echo translator::trans("share.song"); ?></h3>
				<ul class="share-box-list">
					<?php foreach($this->getShareSocial() as $social){ ?>
					<li class="share-linksocial link-<?php echo $social['name']; ?>">
						<a class="tooltips" target="_blank" href="<?php echo $social['link']; ?>" title="<?php echo translator::trans("share.song.on.{$social['name']}"); ?>">
							<i class="fa fa-<?php echo ($social['name'] == "mail" ? "envelope-o" : $social['name']); ?>"></i>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="tags">
					برچسب ها:
					<?php foreach($this->getTags() as $tag){ ?>
						<a href="<?php echo $tag['url']; ?>"><?php echo $tag['content']; ?></a>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="comments-section">
			<div class="row">
				<div class="col-xs-12">
					<h3><?php echo translator::trans("ghafiye.comments"); ?></h3>
					<?php echo $this->revertReply(); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-default panel-comments">
						<div class="panel-body">
							<p class="panel-title"><?php echo translator::trans("ghafiye.songs.add.comments"); ?></p>
							<form method="post">
								<div class="row reply-section">
									<?php
									$this->createField(array(
										"name" => "reply",
										"type" => "hidden",
									));
									?>
									<div class="col-sm-10 col-xs-8">
										<p><span><?php echo translator::trans("ghafiye.comment.replyTo"); ?></span> <strong class="comment-reply-sender-name"></strong></p>
									</div>
									<div class="col-sm-2 col-xs-4">
										<button class="btn btn-sm btn-default btn-block bnt-cancel-reply"><?php echo translator::trans("ghafiye.cancel"); ?></button>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-5 col-xs-12">
										<?php
										$this->createField(array(
											"name" => "name",
											"label" => translator::trans("ghafiye.comment.name"),
											"readonly" => $this->isLogin,
										));
										$this->createField(array(
											"type" => "email",
											"name" => "email",
											"label" => translator::trans("ghafiye.comment.email"),
											"ltr" => true,
											"readonly" => $this->isLogin,
										));
										?>
										<small class="text-muted"><?php echo translator::trans("ghafiye.songs.comment.email.description"); ?></small>
									</div>
									<div class="col-sm-7 col-xs-12">
										<?php $this->createField(array(
											"type" => "textarea",
											"name" => "content",
											"label" => translator::trans("ghafiye.comment.content"),
											"rows" => 4,
										)); ?>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-3 pull-left">
										<button type="submit" class="btn btn-sm btn-block btn-success btn-submit"><i class="fa fa-paper-plane"></i> ارسال</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-3 tools col-md-pull-8">
		<div class="row hidden-xs">
			<div class="col-xs-12">
				<?php if ($this->song->synced != song::synced) { ?>
					<a href="<?php echo base\url("contribute/song/sync/" . $this->song->id); ?>" class="btn btn-sync btn-sm btn-block">
						<div class="btn-icon"><i class="fa fa-clock-o"></i></div>
						<?php echo translator::trans("ghafiye.contribute.sync"); ?>
					</a>
				<?php } ?>
			</div>
		</div>
		<div class="row hidden-xs">
			<div class="col-xs-12">
				<a href="<?php echo base\url("contribute/song/edit/" . $this->song->id); ?>" class="btn btn-sm btn-edit btn-block">
					<div class="btn-icon"><i class="fa fa-pencil"></i></div>
					<?php echo translator::trans("ghafiye.contribute.edit"); ?>
				</a>
			</div>
		</div>
		<div class="row hidden-xs">
			<div class="col-xs-12">
				<a href="<?php echo base\url("contribute/song/translate/" . $this->song->id, $translateUrlParamater); ?>" class="btn btn-sm btn-translate btn-block">
					<div class="btn-icon"><i class="fa fa-language"></i></div>
					<?php echo translator::trans("ghafiye.contribute.translate"); ?>
				</a>
			</div>
		</div>
		<ul class="list-group">
			<a href="#" id="like" class="list-group-item" data-song="<?php echo($this->song->id); ?>">
				<span class="float-xs-right"><i class="fa like-icon <?php echo(($this->getlikeStatus() ? "fa-heart" : "fa-heart-o")); ?>"></i></span>
				<?php echo(translator::trans("songs.likes.number", array('number' => $this->song->likes))); ?>
			</a>
			<li class="list-group-item">
				<span class="float-xs-right"><i class="fa fa-language"></i></span>
				<?php echo(translator::trans("songs.translations.number", array('number' => $numberOfLangs))); ?>
			</li>
			<?php if ($this->song->genre) { ?>
			<li class="list-group-item">
				<span class="float-xs-right"><i class="fa fa-tag"></i></span>
				<a href="<?php echo base\url('explore/genre/'.$this->song->genre->encodedTitle()); ?>"><?php echo($this->song->genre->title()); ?></a>
			</li>
			<?php } ?>
		</ul>
		<?php if ($songs = $this->getSongs()) { ?>
		<div class="panel">
			<div class="panel-heading">
				<a class="album-name" href="<?php echo(base\url($this->singer->encodedName($lang).'/albums/'.$this->song->album->encodedTitle($lang))); ?>">
					<div class="col-sm-5 col-xs-6">
						<img src="<?php echo $this->song->album->getImage(50, 50); ?>" alt="<?php echo $this->song->album->encodedTitle(); ?> <?php echo $this->singer->encodedName(); ?> - cover art">
					</div>
					<div class="col-sm-7 col-xs-6">
						<p class=title><?php echo $this->song->album->title($lang); ?></p>
						<span><?php echo date::format('F Y', $this->song->release_at); ?></span>
					</div>
				</a>
			</div>
			<div class="panel-body">
				<ul>
					<?php
					$i = 0;
					foreach($songs as $song){
					?>
					<li class="row">
						<div class="col-sm-2 col-xs-2">
							<span class="text-center"><?php echo ++$i; ?></span>
						</div>
						<div class="col-sm-2 col-xs-2">
							<img src="<?php echo $song->getImage(32, 32); ?>" alt="<?php echo $song->title(); ?>">
						</div>
						<div class="col-sm-8 col-xs-8">
							<a href="<?php echo(base\url($this->singer->encodedName($lang).'/'.$song->encodedTitle($lang))); ?>"><strong><?php echo $song->title($lang); ?></strong></a>
						</div>
					</li>
					<?php
					}
					?>
				</ul>
				<?php if($this->isMoreSong()){ ?>
				<a href="<?php echo base\url($this->singer->encodedName($lang).'/albums/'.$this->song->album->encodedTitle($lang)); ?>" class="more"><?php echo translator::trans('home.section.toplyrics.more'); ?></a>
				<?php } ?>
			</div>
		</div>
		<?php
			echo $this->getSideAd();
		}
		if ($songs = $this->getPopularSongs()) { ?>
		<div class="panel panel-songs">
			<div class="panel-heading">
				<?php echo translator::trans('lyrics.top'); ?>
				<?php if($this->isMorePopularSong()){ ?>
				<a class="pull-left" href="<?php echo base\url($this->singer->encodedName($lang)); ?>"><?php echo translator::trans('home.section.toplyrics.more'); ?> <i class="fa fa-angle-left"></i></a>
				<?php } ?>
			</div>
			<div class="panel-body">
				<ul>
					<?php
					$i = 0;
					foreach($songs as $song){
						$isCurrentSong = $this->song->id == $song->id;
					?>
					<li class="row<?php echo $isCurrentSong ? ' active' : ''; ?>">
						<div class="col-sm-2 col-xs-2">
							<span class="text-center"><?php ++$i; echo $isCurrentSong ? '<i class="fa fa-play-circle"></i>': $i; ?></span>
						</div>
						<div class="col-sm-2 col-xs-2">
							<img src="<?php echo $song->getImage(32, 32); ?>" alt="<?php echo $song->title(); ?>">
						</div>
						<div class="col-sm-8 col-xs-8">
							<a <?php if(!$isCurrentSong){ ?> href="<?php echo(base\url($this->singer->encodedName($lang).'/'.$song->encodedTitle($lang))); ?>"<?php } ?>><strong><?php echo $song->title($lang);?></strong></a>
						</div>
					</li>
					<?php
					}
					?>
				</ul>
			</div>
		</div>
		<?php
		}
		echo $this->getSideAd();
		?>
	</div>
</div>
<?php if($albums = $this->getAlbums()){ ?>
<div class="row">
	<div class="col-sm-12">
		<div class="more-albums">
			<span class="more-albums-title"><?php echo translator::trans('more.albums.for', ['url'=>base\url($this->singer->encodedName($lang)), 'name'=>$this->singer->name($lang)]); ?></span>
			<div class="row">
				<?php foreach($albums as $album){ ?>
				<div class="col-sm-3">
					<a class="album" href="<?php echo(base\url($this->singer->encodedName($lang).'/albums/'.$album->encodedTitle($lang))); ?>">
						<div class="image" style="background-image: url(<?php echo $album->getImage(262.5, 240); ?>);"></div>
						<div class="description">
							<ul class="info pull-right">
								<li class="title"><?php echo $album->title(); ?></li>
								<li class="year"><?php echo date::format('Y', $this->getAlbumReleaseDate($album)); ?></li>
							</ul>
						</div>
					</a>
				</div>
				<?php } ?>
			</div>
			<?php if($this->isMoreAlbum()){ ?>
			<a href="<?php echo base\url($this->singer->encodedName($lang).'/albums'); ?>" class="more"><?php echo translator::trans('home.section.toplyrics.more'); ?></a>
			<?php } ?>
		</div>
	</div>
</div>
<?php
	}
$this->the_footer();
