<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#####################################
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
<meta http-equiv=Content-Type content="text/html; charset=<?php echo CHARSET; ?>">
<link rel="shortcut icon" href="favicon.ico" />
<title><?php echo TITLE_FINISHED; ?></title>
<style type='text/css' media='all'>@import url('includes/style.css');</style>
</head>
<body>

		
		
	
	
		<div id="content-box">
			<div id="content-pad">
				

	<div id="stepbar">
		<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">

			<h1><?php echo STEPS ;?></h1>
<div class="step-off"><a href="index.php"><?php echo START; ?></a></div>
<div class="step-off"><?php echo STEP1; ?></div>
<div class="step-off"><?php echo STEP2; ?></div>
<div class="step-off"><?php echo STEP3; ?></div>
<div class="step-off"><?php echo STEP4; ?></div>
<div class="step-off"><?php echo STEP5; ?></div>
<div class="step-off"><?php echo STEP6; ?></div>
<div class="step-on"><?php echo END; ?></div>

		<div class="box"></div>
  	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
  </div>






<div id="right">
	<div id="rightpad">
		<div id="step">
			<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
		</div>
		<div class="m">

				<div class="far-right">
					
			
						
				</div>
				<span class="step"><?php echo TITLE_FINISHED; ?></span>
			</div>
		<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
		</div>
	</div>

	<div id="installer">
			<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
		</div>
		<div class="m">

		


<?php echo TEXT_WELCOME_FINISHED; ?><br><br>
<?php echo TEXT_SHOP_CONFIG_SUCCESS; ?><br><br>
<?php echo TEXT_TEAM; ?><br><br>

				
				<div class="install-body">
				

					<div class="clr"></div>
					
				</div>
	
			
			
			
		<div class="newsection"></div>
				
		
		
		</div>
		<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
		</div>
		</div>
	</div>
</div>

<div class="clr"></div>




			</div>
		</div>

		
<?php echo _copy(); ?>
</body>
</html>