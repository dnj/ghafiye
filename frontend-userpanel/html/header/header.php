<?php
use \packages\base;
use \packages\base\{translator, frontend\theme};
use \themes\clipone\navigation;
use \packages\userpanel;
use packages\userpanel\{authorization, authentication};

?>
<!DOCTYPE html>
<html dir="rtl" lang="<?php echo translator::getShortCodeLang(); ?>">

<head>
    <meta charset="utf-8">
	<title><?php echo $this->getTitle(); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="<?php echo theme::url('assets/images/favicon.ico'); ?>" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo theme::url('assets/images/favicon.ico'); ?>" type="image/x-icon">
	<?php
	$description = $this->getDescription();
	if($description){
		echo("<meta content=\"{$description}\" name=\"description\" />");
	}
	$this->buildMetaTags();
	$this->loadCSS();
	?>
</head>
<body class="<?php echo $this->genBodyClasses(); ?>">
	<header>
		<div class="container">
			<nav class="navbar">
				<div class="navbar-header">
					<a class="logo" href="<?php echo base\url(); ?>">
						<img src="<?php echo theme::url('assets/images/logo-48x48.png'); ?>" alt="Logo">
						<span>قــا</span>
						<span>فــ&zwj;</span>
						<span>یـه</span>
					</a>
					<button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button"><span class="clip-list-2"></span></button>
				</div>
				<ul class="nav navbar-right">
					<li><a href="<?php echo base\url("contribute"); ?>"><?php echo translator::trans("ghafiye.contribute"); ?></a></li>
					<li><a href="<?php echo base\url("community"); ?>"><?php echo translator::trans("ghafiye.community"); ?></a></li>
					<li><a href="<?php echo base\url('explore'); ?>"><?php echo translator::trans('toplyrics'); ?></a></li>
				</ul>
				<ul class="nav navbar-left">
					<li class="dropdown current-user">
						<a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" data-close-others="true" href="#">
							<img src="<?php echo $this->getSelfAvatarURL(); ?>" width="30" height="30" class="circle-img" alt="">
							<span class="username"><?php echo authentication::getName(); ?></span>
							<i class="clip-chevron-down"></i>
						</a>
						<ul class="dropdown-menu">
							<?php if ($this->canViewProfile()) { ?>
							<li><a href="<?php echo userpanel\url("profile/view"); ?>"><i class="clip-user-2"></i>&nbsp;<?php echo translator::trans("profile.view"); ?></a></li>
							<li class="divider"></li>
							<?php } ?>
							<li><a href="<?php echo base\url("userpanel/lock"); ?>"><i class="clip-locked"></i>&nbsp;خروج موقت </a></li>
							<li><a href="<?php echo base\url("userpanel/logout"); ?>"><i class="clip-exit"></i> &nbsp;خروج </a></li>
						</ul>
					</li>
				</ul>
			</nav>
		</div>
	</header>
	<main class="container">
		<div class="row">
			<div class="col-sm-3">
				<div class="navbar-content navbar-collapse collapse">
					<div class="main-navigation">
						<div class="navigation-toggler">
							<i class="clip-chevron-left"></i>
							<i class="clip-chevron-right"></i>
						</div>
						<ul class="main-navigation-menu">
							<?php
							echo navigation::build();
							?>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-sm-9 col-xs-12">
				<div class="row">
					<div class="col-xs-12 errors">
					<?php
					if($errorcode = $this->getErrorsHTML()){
						echo $errorcode;
					}
					?></div>
				</div>
