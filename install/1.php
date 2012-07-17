<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/
  
require('includes/top.php');

if (!isset($_SESSION['language']) )
{
   $_SESSION['language'] = 'ru';
}   

if (isset($_POST['LANGUAGE']))
{
   $_SESSION['language'] = $_POST['LANGUAGE'];
}

include(dirname(__FILE__).'/lang/'.$_SESSION['language'].'/lang.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TEXT_SETUP_INDEX; ?></title>
<link rel="shortcut icon" href="favicon.ico" />
<style type='text/css' media='all'>@import url('includes/install.css');</style>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<script src="bootstrap/jquery.js"></script>
<script src="bootstrap/js/bootstrap-transition.js"></script>
<script src="bootstrap/js/bootstrap-alert.js"></script>
<script src="bootstrap/js/bootstrap-modal.js"></script>
<script src="bootstrap/js/bootstrap-dropdown.js"></script>
<script src="bootstrap/js/bootstrap-scrollspy.js"></script>
<script src="bootstrap/js/bootstrap-tab.js"></script>
<script src="bootstrap/js/bootstrap-tooltip.js"></script>
<script src="bootstrap/js/bootstrap-popover.js"></script>
<script src="bootstrap/js/bootstrap-button.js"></script>
<script src="bootstrap/js/bootstrap-collapse.js"></script>
<script src="bootstrap/js/bootstrap-carousel.js"></script>
<script src="bootstrap/js/bootstrap-typeahead.js"></script>
</head>
<body>

<div class="container">
	<p></p>
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>

				<a class="brand" href=""><?php echo TEXT_SETUP_INDEX; ?></a>

				<?php echo lang_menu(); ?>
			</div>
		</div>
	</div>

	<form action="" method="post" name="language">
	<input type="hidden" name="LANGUAGE" id="lang_a" value="" />
	</form>

	<!-- FORM START -->
	<form name="install" method="post" action="2.php" class="form-horizontal">
		<input type="hidden" name="task" value="" />

		<div class="page-header">

			<span class="pull-right">
				<a class="btn" href="index.php" title="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a>
				<a class="btn btn-success" onclick="document.install.submit();" title="<?php echo IMAGE_CONTINUE; ?>"><?php echo IMAGE_CONTINUE;?></a>
			</span>

			<h1><?php echo STEPS ;?> <?php echo STEP1; ?></h1>
		</div>

		<div class="progress progress-striped active">
			<div class="bar" style="width:25%;"></div>
		</div>

		<input type="hidden" name="install[]" value="database" checked="checked" />
		<input type="hidden" name="install[]" value="configure" checked="checked" />
		<?php echo os_draw_input_field_installer('DIR_FS_WWW_ROOT', $_dir_fs_www_root,'hidden','size="60"'); ?>
		<?php echo os_draw_input_field_installer('WWW_ADDRESS', $_www_location,'hidden','size="60"'); ?>

		<fieldset>
			<legend><?php echo TITLE_DATABASE_SETTINGS; ?> <small><?php echo TEXT_WELCOME_STEP1; ?></small></legend>

			<div class="control-group">
				<label class="control-label" for="input01"><?php echo TEXT_DATABASE_SERVER;?></label>
				<div class="controls">
					<?php echo os_draw_input_field_installer('DB_SERVER', DEFAULT_DB_SERVER, 'text'); ?>
					<p class="help-block"><?php echo TEXT_DATABASE_SERVER_LONG; ?></p>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="input01"><?php echo TEXT_USERNAME; ?></label>
				<div class="controls">
					<?php echo os_draw_input_field_installer('DB_SERVER_USERNAME', DEFAULT_DB_SERVER_USERNAME, 'text'); ?>
					<p class="help-block"><?php echo TEXT_USERNAME_LONG; ?></p>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="input01"><?php echo TEXT_PASSWORD; ?></label>
				<div class="controls">
					<?php echo os_draw_password_field_installer('DB_SERVER_PASSWORD', DEFAULT_DB_SERVER_PASSWORD, 'text'); ?>
					<p class="help-block"><?php echo TEXT_PASSWORD_LONG; ?></p>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="input01"><?php echo TEXT_DATABASE; ?></label>
				<div class="controls">
					<?php echo os_draw_input_field_installer('DB_DATABASE', DEFAULT_DB_DATABASE, 'text'); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="input01"><?php echo TEXT_DB_PREFIX;?></label>
				<div class="controls">
					<?php echo os_draw_input_field_installer('DB_PREFIXX', 'os','text'); ?>
				</div>
			</div>
		</fieldset>

		<span class="pull-right">
			<a class="btn" href="index.php" title="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a>
			<a class="btn btn-success" onclick="document.install.submit();" title="<?php echo IMAGE_CONTINUE; ?>"><?php echo IMAGE_CONTINUE;?></a>
		</span>
		<div class="clear"></div>

	</form>
	<!-- FORM END -->

	<hr>

	<footer>
		<p><?php echo _copy(); ?></p>
	</footer>
</div>
</body>
</html>