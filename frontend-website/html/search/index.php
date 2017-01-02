<?php
use \packages\base;
use \packages\base\translator;
use \packages\ghafiye\song\person as songPerson;
$this->the_header();
?>
<h1><?php echo(translator::trans("result.searchBy", array("word"=>$this->getWord()))) ?></h1>
<div class="row tabs">
	<ul class="list-inline">
		<li>
	        <a class="<?php if(!$this->getType())echo("selected"); ?>" href="<?php echo(base\url("search/{$this->getWord()}")); ?>">
	            <?php echo(translator::trans("all-result-search")); ?>
	        </a>
	    </li>
		<?php if(!$this->getResults()["songs"]){ ?>
	    <li>
	        <a class="<?php if($this->getType() == "songs")echo("selected"); ?>" href="<?php echo(base\url("search/{$this->getWord()}/songs")); ?>">
	            <?php echo(translator::trans("artist.songs")); ?>
	        </a>
	    </li>
		<?php }
		if(!$this->getResults()["persons"]){ ?>
	    <li>
	        <a class="<?php if($this->getType() == "persons")echo("selected"); ?>" href="<?php echo(base\url("search/{$this->getWord()}/persons")); ?>">
	            <?php echo(translator::trans("artist.persons")); ?>
	        </a>
	    </li>
		<?php }
		if(!$this->getResults()["lyrics"]){ ?>
	    <li>
	        <a class="<?php if($this->getType() == "lyrics")echo("selected"); ?>" href="<?php echo(base\url("search/{$this->getWord()}/lyrics")); ?>">
	            <?php echo(translator::trans("artist.lyrics")); ?>
	        </a>
	    </li>
		<?php } ?>
	</ul>
</div>
<div class="row">
	<?php if(!$this->getType() and $this->getPersons()) $this->the_sidebar('search'); ?>
	<div class="<?php echo($this->getType() ? "col-md-10 col-md-offset-1" : "col-md-8"); ?> container">
		<div class="panel">
			<div class="panel-heading"><?php $title = ($this->getType() ? $this->getType() : "songs"); echo translator::trans("artist.{$title}"); ?></div>
			<ul class="results">
				<?php
				$x = 0;
				foreach($this->results() as $result){ ?>
					<li>
						<span><?php echo(++$x); ?></span>
						<img src="<?php echo($result['image']); ?>" alt="<?php echo($result['title']); ?>" />
						<div>
							<a href="<?php echo(base\url($result['link'])); ?>"><strong><?php echo($result['title']); ?></strong></a>
							<?php if(isset($result['signer'])){ ?>
							<a href="<?php echo(base\url($result["signer"]));  ?>"><?php echo($result["signer"]); ?></a>
							<?php } ?>
						</div>
					</li>
				<?php } ?>
			</ul>
			<?php echo $this->pager(); ?>
		</div>
	</div>
</div>
<?php $this->the_footer(); ?>
