<?php
use packages\base;
use packages\base\{http, translator, frontend\theme};
use packages\userpanel;
use packages\ghafiye\authentication;
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
			<nav>
				<a class="logo" href="<?php echo base\url(); ?>">
					<img src="<?php echo theme::url('assets/images/logo-48x48.png'); ?>" alt="Logo">
					<span>قــا</span>
					<span>فــ&zwj;</span>
					<span>یـه</span>
				</a>
				<ul>
					<?php
					if (authentication::check()) {
						$user = authentication::getUser();
					?>
						<li>
							<a href="<?php echo base\url("profile"); ?>">
								<img class="img-circle" src="<?php echo $user->getAvatar(32, 32); ?>" alt="<?php echo $user->getFullName(); ?>">
							</a>
						</li>
					<?php } else { ?>
					<li><a href="<?php echo userpanel\url("login", array("backTo" => http::$request["uri"])); ?>"><?php echo translator::trans("ghafiye.login"); ?></a></li>
					<li><a href="<?php echo userpanel\url("register", array("backTo" => http::$request["uri"])); ?>"><?php echo translator::trans("ghafiye.register"); ?></a></li>
					<?php } ?>
					<li class="divider"></li>
					<li><a href="<?php echo base\url("contribute"); ?>"><?php echo translator::trans("ghafiye.contribute"); ?></a></li>
					<li><a href="<?php echo base\url("community"); ?>"><?php echo translator::trans("ghafiye.community"); ?></a></li>
					<li><a href="<?php echo base\url('explore'); ?>"><?php echo translator::trans('toplyrics'); ?></a></li>
				</ul>
				<form class="searchbox hidden-xs hidden-sm hidden-md">
					<div class="form-group has-icon">
						<i class="fa fa-search form-control-icon"></i>
						<input type="text" name="word" placeholder="<?php echo translator::trans('home.searchbox.placeholder'); ?>">
					</div>
				</form>
			</nav>

		</div>
	</header>
	<main class="container">
		<div class="row">
			<div class="col-xs-12 errors">
			<?php
			if($errorcode = $this->getErrorsHTML()){
				echo $errorcode;
			}
			?></div>
		</div>
