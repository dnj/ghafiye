<?php
use \packages\base;
use \packages\base\translator;
?>
<div class="col-md-4">
	<div class="panel">
		<div class="panel-heading"><?php echo translator::trans("artist.persons"); ?></div>
		<ul class="results">
			<?php $x=0;
			 foreach($this->getPersons() as $person){ ?>
				<li>
					<span><?php echo(++$x); ?></span>
					<img src="<?php echo($person->getAvatar(32, 32)); ?>" alt="<?php echo($person->name($person->showing_lang)); ?>" />
					<div>
						<a href="<?php echo(base\url($person->encodedName($person->showing_lang))); ?>"><strong><?php echo($person->name($person->showing_lang)); ?></strong></a>
					</div>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>
