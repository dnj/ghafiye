<?php
use \packages\base;
use \packages\base\translator;
use \packages\ghafiye\song\lyric;
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
			<?php if($this->song->lang != 'fa'){ ?>
			<a <?php if($this->getLyricsLanguage() == 'fa')echo('class="active"'); ?> href="<?php echo(base\url($this->singer->encodedName('fa')).'/'.$this->song->encodedTitle('fa')); ?>"><?php echo translator::trans('translations.langs.fa'); ?></a>
			<?php
			}
			if($numberOfLangs - 2 > 0){
			?>
			<select class="selectpicker"  data-width="fit"  title="<?php echo translator::trans('translations.langs.more', array('number' => $numberOfLangs -2)); ?>">
				<?php
				foreach($this->langs() as $lang){
					if(in_array($lang, array($this->song->lang, 'fa'))){
						continue;
					}
				?>
					<option value="<?php echo $lang; ?>" <?php if($this->is_ltr($lang))echo('class="ltr"'); ?> data-link="<?php echo(base\url($this->singer->encodedName($lang)).'/'.$this->song->encodedTitle($lang)); ?>"><?php echo translator::trans('translations.langs.'.$lang); ?></option>
				<?php } ?>
			</select>
			<?php } ?>
		</div>
	</div>
</header>
<div class="row">
	<div class="col-sm-3 tools">
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
				<?php echo($this->song->genre->title()); ?>
			</li>
		</ul>
	</div>
	<section class="col-sm-9 col-md-7 text col-md-offset-1">
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
</div>
<?php
$this->the_footer();
