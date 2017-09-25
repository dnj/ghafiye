<?php
use \packages\base;
use \packages\userpanel\date;
use \packages\base\translator;
use \packages\ghafiye\song\lyric;
use \packages\ghafiye\song\person;
use \packages\ghafiye\person\name as personName;
$this->the_header();
$numberOfLangs = $this->numberOfLangs();
$lang = $this->getLyricsLanguage();
?>
<header class="row">
	<div class="col-sm-3">
		<img src="<?php echo $this->song->getImage(255, 255); ?>" class="song-image">
	</div>
	<div class="col-sm-9 title">
		<h1><?php echo $this->song->title($lang); ?></h1>
		<h2><a href="<?php echo(base\url($this->singer->encodedName($lang))); ?>"><?php echo $this->singer->name($lang); ?></a></h2>
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
			<?php foreach($this->getOrginalLyrices() as $lyric){ ?>
			<p>
				<span <?php if($this->is_ltr($lyric->lang))echo('class="ltr"'); ?>><?php echo $lyric->text; ?></span>
				<?php
				$translate = $this->getTranslateLyricById($lyric->id);
				if($translate and $lang != $this->song->lang){
				?>
				<span <?php if($this->is_ltr($translate->lang))echo('class="ltr"'); ?>><?php echo $translate->text; ?></span>
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
	</div>
	<div class="col-sm-3 tools col-md-pull-8">
		<ul class="list-group">
			<a href="#" id="like" class="list-group-item" data-song="<?php echo($this->song->id); ?>">
				<span class="float-xs-right"><i class="fa like-icon <?php echo(($this->getlikeStatus() ? "fa-heart" : "fa-heart-o")); ?>"></i></span>
				<?php echo(translator::trans("songs.likes.number", array('number' => $this->song->likes))); ?>
			</a>
			<li class="list-group-item">
				<span class="float-xs-right"><i class="fa fa-language"></i></span>
				<?php echo(translator::trans("songs.translations.number", array('number' => $numberOfLangs))); ?>
			</li>
			<li class="list-group-item">
				<span class="float-xs-right"><i class="fa fa-tag"></i></span>
				<a href="<?php echo base\url('explore/genre/'.$this->song->genre->encodedTitle()); ?>"><?php echo($this->song->genre->title()); ?></a>
			</li>
		</ul>
		<?php if($songs = $this->getSongs()){ ?>
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
		<?php } ?>
		<?php if($songs = $this->getPopularSongs()){ ?>
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
		<?php } ?>
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
<?php } ?>
<?php
$this->the_footer();
