<?php
use \packages\base;
use \packages\base\{translator, frontend\theme};
use \packages\userpanel;
use \packages\ghafiye\authentication;
?>
<!DOCTYPE html>
<html dir="rtl" lang="<?php echo translator::getShortCodeLang(); ?>">

<head>
    <meta charset="utf-8">
	<title><?php echo $this->getTitle(); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="google-site-verification" content="QJ5ExDHPeGh0ibENsXRe_tFwfKzEPMz8yNEXNSYSzWs" />
	<link rel="icon" href="<?php echo theme::url('assets/images/favicon.ico'); ?>" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo theme::url('assets/images/favicon.ico'); ?>" type="image/x-icon">
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
				<a class="logo" href="<?php echo base\url(); ?>">
					<img src="<?php echo theme::url('assets/images/logo-70x70.png'); ?>" alt="Logo">
					<span>قــا</span>
					<span>فــ&zwj;</span>
					<span>یـه</span>
				</a>
				<ul>
					<?php if (authentication::check()) { ?>
						<li><a href="<?php echo base\url("contribute"); ?>"><?php echo translator::trans("ghafiye.contribute"); ?></a></li>
					<?php } else { ?>
						<li><a href="<?php echo userpanel\url("login"); ?>"><?php echo translator::trans("ghafiye.login"); ?></a></li>
						<li><a href="<?php echo userpanel\url("register"); ?>"><?php echo translator::trans("ghafiye.register"); ?></a></li>
					<?php } ?>
					<li class="divider"></li>
					<li><a href="<?php echo base\url("community"); ?>"><?php echo translator::trans("ghafiye.community"); ?></a></li>
					<li><a href="<?php echo base\url('explore'); ?>"><?php echo translator::trans('toplyrics'); ?></a></li>
				</ul>
			</nav>
			<form class="searchbox">
				<h1><?php echo translator::trans('home.title'); ?></h1>
				<p><?php echo translator::trans('home.title.description'); ?></p>
				<div class="row">
					<div class="col-sm-8 col-sm-offset-2">
						<div class="form-group has-icon">
							<i class="fa fa-search form-control-icon"></i>
							<input type="text" name="word" placeholder="<?php echo translator::trans('home.searchbox.placeholder'); ?>">
						</div>
					</div>
				</div>
			</form>
		</div>
	</header>
