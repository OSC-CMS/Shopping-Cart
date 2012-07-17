<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.6
#####################################
*/

require('includes/top.php');
if (!isset($_SESSION['language']) )
{
   $_SESSION['language'] = 'ru';
}   
include('lang/'.$_SESSION['language'].'/lang.php');

if (isset($_POST['LANGUAGE']))
{
   $_SESSION['language'] = $_POST['LANGUAGE'];
}
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
<form action="" method="post" name="language">
<input type="hidden" name="LANGUAGE" id="lang_a" value="" />
</form>

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





	<div class="page-header">

		<span class="pull-right">
			<a class="btn" href="index.php" title="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a>
			<a class="btn btn-success" onclick="document.install.submit();" title="<?php echo IMAGE_CONTINUE; ?>"><?php echo IMAGE_CONTINUE;?></a>
		</span>

		<h1><?php echo STEPS ;?> <?php echo STEP4; ?></h1>
	</div>

	<div class="progress progress-striped active">
		<div class="bar" style="width:62.5%;"></div>
	</div>



<div class="alert alert-info">
<?php echo TEXT_WELCOME_STEP4; ?>
</div>


<?php  if ( ( (file_exists(DIR_FS_CATALOG . 'config.php')) && (!is_writeable(DIR_FS_CATALOG . 'config.php')) )  )  {
?>
<div class="contacterror">
<strong><?php echo TITLE_STEP4_ERROR; ?></strong>
</div>

<div class="boxMe"><?php echo TEXT_STEP4_ERROR; ?>
<ul class="boxMe">
<li>cd <?php echo DIR_FS_CATALOG; ?>/</li>
<li>touch config.php</li>
<li>chmod 777 config.php </li>
</ul>
</div>

<p class="noteBox">
<?php echo TEXT_STEP4_ERROR_1; ?>
</p>

<p class="noteBox">
<?php echo TEXT_STEP4_ERROR_2; ?>
</p>

<form name="install" action="step4.php" method="post">
<?php
reset($_POST);
while (list($key, $value) = each($_POST)) 
{
   if ($key != 'x' && $key != 'y')
   {
      if (is_array($value)) 
	  {
         for ($i=0; $i<sizeof($value); $i++) 
		 {
            echo os_draw_hidden_field_installer($key . '[]', $value[$i]);
         }
      } 
	  else 
	  {
         echo os_draw_hidden_field_installer($key, $value);
      }
   }
}

?>              
<a href="index.php"><img src="images/button_cancel.gif" border="0" alt="<?php echo IMAGE_CANCEL; ?>" /></a>&nbsp;
<input type="image" src="images/button_retry.gif" alt="<?php echo IMAGE_RETRY; ?>" />
</form>

<?php
  } else {
?>
<div class="form-horizontal">
<form name="install" action="5.php" method="post">
<p class="bold"><?php echo TEXT_VALUES; ?></p>

<?php
reset($_POST);
while (list($key, $value) = each($_POST)) 
{
   if ($key != 'x' && $key != 'y') 
   {
      if (is_array($value)) 
	  {
         for ($i=0; $i<sizeof($value); $i++) 
		 {
            echo os_draw_hidden_field_installer($key . '[]', $value[$i]);
         }
       } 
	  else 
	  {
         echo os_draw_hidden_field_installer($key, $value);
      }
   }
}
?>

	<hr>

	<fieldset>
		<legend><?php echo TITLE_DATABASE_SETTINGS; ?></legend>
		<div class="control-group">
			<label class="control-label" for=""><?php echo TEXT_PERSIST; ?></label>
			<div class="controls">
				<?php echo os_draw_checkbox_field_installer('USE_PCONNECT', 'true'); ?>
				<br />
				<?php echo TEXT_PERSIST_LONG; ?>
			</div>
		</div><hr>
		<div class="control-group">
			<label class="control-label" for=""><?php echo TEXT_SESS_LONG; ?></label>
			<div class="controls">
				<label class="radio"><?php echo os_draw_radio_field_installer('STORE_SESSIONS', 'files', true); ?><?php echo TEXT_SESS_FILE; ?></label>
				<label class="radio"><?php echo os_draw_radio_field_installer('STORE_SESSIONS', 'mysql',false); ?><?php echo TEXT_SESS_DB; ?></label>

			</div>
		</div>
	</fieldset>


<input type="hidden" name="install[]" value="configure" />
<input type="hidden" name="task" value="" />
</form>
</div>
<?php
  }
?>





	<span class="pull-right">
		<a class="btn" href="index.php" title="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a>
		<a class="btn btn-success" onclick="document.install.submit();" title="<?php echo IMAGE_CONTINUE; ?>"><?php echo IMAGE_CONTINUE;?></a>
	</span>
	<div class="clear"></div>



	<hr>

	<footer>
		<p><?php echo _copy(); ?></p>
	</footer>
</div>

</body>
</html>