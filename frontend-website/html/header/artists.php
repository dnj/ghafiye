<?php
use \packages\base;
use \packages\base\translator;
use \packages\userpanel\date;
use \packages\ghafiye\song\person;
$this->the_header();
$lang = $this->getSongLanguage();
?>
<header class="row">
	<div class="cover<?php if(!$this->artist->cover){echo(" no-cover");} ?>"<?php if($this->artist->cover){echo(" style=\"background-image:url('".$this->artist->getCover(1170, 370)."');\"");} ?>></div>
	<div class="col-md-2 col-sm-3 hidden-xs">
		<img src="<?php echo $this->artist->getAvatar(130, 130); ?>" class="avatar">
	</div>
	<div class="col-md-10 col-sm-9 col-xs-12 info">
		<span><?php echo translator::trans('artist.lyrics'); ?></span>
		<h1><?php echo $this->artist->name($lang); ?></h1>
		<ul class="genres"><?php
		foreach($this->getAristGenres() as $genre){
			echo("<li><a href=\"".base\url('explore/genre/'.$genre->encodedTitle())."\">".$genre->title()."</a></li>");
		}
		?></ul>
	</div>
</header>
<nav class="row">
	<ul>
		<li<?php if($this instanceof themes\musixmatch\views\artists\view)echo(' class="active"'); ?>><a href="<?php echo base\url($this->artist->encodedName($lang)); ?>"><?php echo translator::trans('artist.songs'); ?></a></li>
		<?php if ($this->getAlbums()) { ?>
		<li<?php if($this instanceof themes\musixmatch\views\artists\albums)echo(' class="active"'); ?>><a href="<?php echo base\url($this->artist->encodedName($lang)."/albums"); ?>"><?php echo translator::trans('artist.albums'); ?></a></li>
		<?php } ?>
	</ul>
</nav>
