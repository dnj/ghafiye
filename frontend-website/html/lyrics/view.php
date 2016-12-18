<?php
use \packages\base;
use \packages\base\translator;
use \packages\ghafiye\song\lyric;
$this->the_header();
$numberOfLangs = $this->numberOfLangs();
$lang = $this->getLyricsLanguage();
?>
<header class="row">
	<div class="col-xs-3">
		<img src="<?php echo $this->songImage($this->song); ?>" class="song-image">
	</div>
	<div class="col-xs-9 title">
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
	<div class="col-sm-3">
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
