<?php
use \packages\base;
use \packages\userpanel\date;
use \packages\base\translator;
use \packages\ghafiye\song\lyric;
use \packages\ghafiye\song\person;
$this->the_header();
$numberOfLangs = $this->numberOfLangs();
$lang = $this->getLyricsLanguage();
?>
<header class="row">
	<div class="col-sm-3">
		<img src="<?php echo $this->songImage($this->song); ?>" class="song-image">
	</div>
	<div class="col-sm-9 title">
		<h1><?php echo $this->song->title($lang); ?></h1>
		<h2><a href="<?php echo(base\url($this->singer->encodedName($lang))); ?>"><?php echo $this->singer->name($lang); ?></a></h2>
		<div class="translations">
			<span><i class="fa fa-language"></i> <?php echo translator::trans('translations'); ?></span>
			<a <?php if($this->getLyricsLanguage() == $this->song->lang)echo('class="active"'); ?> href="<?php echo(base\url($this->singer->encodedName($this->song->lang)).'/'.$this->song->encodedTitle($this->song->lang)); ?>"><?php echo translator::trans('translations.langs.original'); ?></a>
			<?php if($this->song->lang != 'fa' and $this->isLang('fa')){ ?>
			<a <?php if($this->getLyricsLanguage() == 'fa')echo('class="active"'); ?> href="<?php echo(base\url($this->singer->encodedName('fa')).'/'.$this->song->encodedTitle('fa')); ?>"><?php echo translator::trans('translations.langs.fa'); ?></a>
			<?php
			}
			if($numberOfLangs - 2 > 0){
			?>
			<select class="selectpicker"  data-width="fit"  title="<?php echo translator::trans('translations.langs.more', array('number' => $numberOfLangs -2)); ?>">
				<?php
				foreach($this->getLangs() as $lang){
				?>
				<option value="<?php echo $lang; ?>" <?php if($this->is_ltr($lang))echo('class="ltr"'); ?> data-link="<?php echo(base\url($this->singer->encodedName($lang)).'/'.$this->song->encodedTitle($lang)); ?>"><?php echo translator::trans('translations.langs.'.$lang); ?></option>
				<?php } ?>
			</select>
			<?php } ?>
		</div>
	</div>
</header>
<div class="row">
	<div class="col-md-7 col-sm-9 col-md-offset-1 col-md-push-3">
		<section class="text">
			<?php foreach($this->getLyrices() as $lyric){ ?>
			<p>
				<?php
				if($lyric->parent){
					$lyric->parent = lyric::byId($lyric->parent);
				?>
				<span <?php if($this->is_ltr($lyric->parent->lang))echo('class="ltr"'); ?>><?php echo $lyric->parent->text; ?></span>
				<?php } ?>
				<span <?php if($this->is_ltr($lyric->lang))echo('class="ltr"'); ?>><?php echo $lyric->text; ?></span>
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
				<a href="<?php echo(base\url($this->singer->encodedName($lang).'/albums/'.$this->song->album->encodedTitle($lang))); ?>">
					<div class="col-sm-5 col-xs-6">
						<img src="<?php echo $this->getAlbumImage(); ?>" alt="25 Adele - cover art">
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
						$singer = $song->getPerson(person::singer);
					?>
					<li class="row">
						<div class="col-sm-2 col-xs-2">
							<span><?php echo ++$i; ?></span>
						</div>
						<div class="col-sm-2 col-xs-2">
							<img src="<?php echo $this->songImage($song); ?>" alt="<?php echo $song->title(); ?>">
						</div>
						<div class="col-sm-8 col-xs-8">
							<a href="<?php echo(base\url($singer->encodedName($lang).'/'.$song->encodedTitle($lang))); ?>"><strong><?php echo $song->title($lang); ?></strong></a>						</div>
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
	</div>
</div>
<?php
$this->the_footer();
