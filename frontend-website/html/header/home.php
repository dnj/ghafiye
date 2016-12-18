<?php
use \packages\base;
use \packages\base\translator;
use \packages\base\frontend\theme;
?>
<!DOCTYPE html>
<html dir="rtl" lang="<?php echo translator::getShortCodeLang(); ?>">

<head>
    <meta charset="utf-8">
	<title><?php echo $this->getTitle(); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	$description = $this->getDescription();
	if($description){
		echo("<meta content=\"{$description}\" name=\"description\" />");
	}
	$this->loadCSS();
	?>
</head>
<body class="<?php echo $this->genBodyClasses(); ?>">
	<header>
		<div class="banner"></div>
		<div class="container">
			<nav>
				<a class="logo" href="<?php echo base\url(); ?>"><img src="<?php echo theme::url('assets/images/logo.white.png'); ?>" alt="Logo"></a>
				<ul>
					<li><a href="<?php echo base\url('explore'); ?>"><?php echo translator::trans('toplyrics'); ?></a></li>
				</ul>
			</nav>
			<form class="searchbox">
				<h1><?php echo translator::trans('home.title'); ?></h1>
				<p><?php echo translator::trans('home.title.description'); ?></p>
				<div class="form-group has-icon">
					<i class="fa fa-search form-control-icon"></i>
					<input type="text" name="word" placeholder="<?php echo translator::trans('home.searchbox.placeholder'); ?>">
				</div>
			</form>
		</div>
	</header>
