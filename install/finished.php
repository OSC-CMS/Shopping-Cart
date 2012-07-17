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

include('lang/'.$_SESSION['language'].'/lang.php');

define('HTTP_SERVER','');
define('HTTPS_SERVER','');
define('DIR_WS_CATALOG','');

$messageStack = new messageStack();
$process = false;
  
if (isset($_POST['action']) && ($_POST['action'] == 'process')) 
{
   $process = true;
   $_SESSION['language'] = os_db_prepare_input($_POST['LANGUAGE']);
   $error = false;

   if ( ($_SESSION['language'] != 'ru') ) 
   {
      $error = true;
      $messageStack->add('index', SELECT_LANGUAGE_ERROR);
   }
        
   if ($error == false) 
   {
      os_redirect(os_href_link('1.php', '', 'NONSSL'));
   }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<link rel="shortcut icon" href="favicon.ico" />
<title><?php echo TITLE_FINISHED; ?></title>
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





	<div class="page-header">
		<h1><?php echo STEPS ;?> <?php echo END; ?></h1>
	</div>

	<div class="progress progress-success progress-striped active">
		<div class="bar" style="width:100%;"></div>
	</div>

	
	<hr>
	<div class="alert alert-success"><?php echo TEXT_WELCOME_FINISHED; ?></div>


<?php echo TEXT_TEAM; ?>



	<hr>

	<footer>
		<p><?php echo _copy(); ?></p>
	</footer>
</div>
</body>
</html>