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

if (isset($_POST['LANGUAGE']))
{
   $_SESSION['language'] = $_POST['LANGUAGE'];
}

include(dirname(__FILE__).'/lang/'.$_SESSION['language'].'/lang.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr" >
<head>
<meta http-equiv=Content-Type content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE_STEP1; ?></title>
<link rel="shortcut icon" href="favicon.ico" />
<style type='text/css' media='all'>@import url('includes/style.css');</style>	
<script type="text/javascript" src="includes/include.js"></script>
</head>
<body>
<form action="" method="post" name="language">
<input type="hidden" name="LANGUAGE" id="lang_a" value="" />
</form> 

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
<div class="step-on"><a><?php echo STEP1; ?></a></div>
<div class="step-off"><a><?php echo STEP2; ?></a></div>
<div class="step-off"><a><?php echo STEP3; ?></a></div>
<div class="step-off"><a><?php echo STEP4; ?></a></div>
<div class="step-off"><a><?php echo STEP5; ?></a></div>
<div class="step-off"><a><?php echo STEP6; ?></a></div>
<div class="step-off"><a><?php echo END; ?></a></div>

		<div class="box"></div>
  	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
  </div>



<form name="install" method="post" action="2.php">


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
					
							
	<div class="button1-right"><div class="prev"><a href="index.php" alt="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a></div></div>
	
<div class="button1-left"><div class="next"><a onclick="document.install.submit();" alt="<?php echo IMAGE_CONTINUE; ?>"><?php echo IMAGE_CONTINUE;?></a></div></div>
				<?php echo lang_menu(); ?>		
				</div>
				<span class="step"><?php echo TITLE_STEP1; ?></span>
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



<span><?php echo TEXT_WELCOME_STEP1; ?></span>
<input type="hidden" name="install[]" value="database" checked="checked" />
<input type="hidden" name="install[]" value="configure" checked="checked" />

<fieldset class="form">
<legend><?php echo TITLE_DATABASE_SETTINGS; ?></legend>
<p><b><?php echo TEXT_DATABASE_SERVER;?></b><br /><?php echo os_draw_input_field_installer('DB_SERVER', DEFAULT_DB_SERVER, 'text'); ?><br /><?php echo TEXT_DATABASE_SERVER_LONG; ?></p>
<p><b><?php echo TEXT_USERNAME; ?></b><br /><?php echo os_draw_input_field_installer('DB_SERVER_USERNAME', DEFAULT_DB_SERVER_USERNAME, 'text'); ?><br /><?php echo TEXT_USERNAME_LONG; ?></p>
<p><b><?php echo TEXT_PASSWORD; ?></b><br /><?php echo os_draw_password_field_installer('DB_SERVER_PASSWORD', DEFAULT_DB_SERVER_PASSWORD, 'text'); ?><br /><?php echo TEXT_PASSWORD_LONG; ?></p>
<p><b><?php echo TEXT_DATABASE; ?></b><br /><?php echo os_draw_input_field_installer('DB_DATABASE', DEFAULT_DB_DATABASE, 'text'); ?><br />
<p><b><?php echo TEXT_DB_PREFIX;?></b><br /><?php echo os_draw_input_field_installer('DB_PREFIXX', 'os','dinn',''); ?><br /></p>
<?php echo os_draw_input_field_installer('DIR_FS_WWW_ROOT', $_dir_fs_www_root,'hidden','size="60"'); ?>
<?php echo os_draw_input_field_installer('WWW_ADDRESS', $_www_location,'hidden','size="60"'); ?>
</fieldset>

<input type="hidden" name="task" value="" />
</form>
				
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