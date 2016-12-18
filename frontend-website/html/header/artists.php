<?php
use \packages\base;
use \packages\base\translator;
use \packages\userpanel\date;
use \packages\ghafiye\song\person;
$this->the_header();
$lang = $this->getSongLanguage();
?>
<header class="row">
	<div class="cover<?php if(!$this->artist->cover){echo(" no-cover");} ?>"<?php if($this->artist->cover){echo(" style=\"background-image:url('".$this->getCoverURL()."');\"");} ?>></div>
	<div class="col-sm-2">
		<img src="<?php echo $this->getAvatarURL(); ?>" class="avatar">
	</div>
	<div class="col-sm-10 info">
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
		<li<?php if($this instanceof themes\musixmatch\views\artists\albums)echo(' class="active"'); ?>><a href="<?php echo base\url($this->artist->encodedName($lang)."/albums"); ?>"><?php echo translator::trans('artist.albums'); ?></a></li>
	</ul>
</nav>
