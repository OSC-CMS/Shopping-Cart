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
<meta http-equiv=Content-Type content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE_STEP4; ?></title>
<link rel="shortcut icon" href="favicon.ico" />
<style type='text/css' media='all'>@import url('includes/style.css');</style>
<script type="text/javascript" src="includes/include.js"></script>
</head>
<body>
<form action="" method="post" name="language">
<input type="hidden" name="LANGUAGE" id="lang_a" value="" />
</form> 
<div id="header1">
   <div id="header2">
      <?php echo install_menu(); ?>
      <div id="header3"></div>
   </div>
</div>
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
<div class="step-on"><?php echo STEP4; ?></div>
<div class="step-off"><?php echo STEP5; ?></div>
<div class="step-off"><?php echo STEP6; ?></div>
<div class="step-off"><?php echo END; ?></div>
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
					
				
	<div class="button1-right"><div class="prev"><a href="index.php" alt="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a></div></div>
<div class="button1-left"><div class="next"><a onclick="document.install.submit();"
 alt="<?php echo IMAGE_CONTINUE;?>"><?php echo IMAGE_CONTINUE;?></a></div></div>
			<?php echo lang_menu(); ?>			
				</div>
				<span class="step"><?php echo TITLE_STEP4; ?></span>
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
		

<?php echo TEXT_WELCOME_STEP4; ?>



<?php echo TITLE_WEBSERVER_CONFIGURATION; ?>


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
            
<form name="install" action="5.php" method="post">
<p>
<b><?php echo TEXT_VALUES; ?></b>
<br />
</p>

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

<fieldset class="form">
<legend><?php echo TITLE_DATABASE_SETTINGS; ?></legend>
<p><?php echo os_draw_checkbox_field_installer('USE_PCONNECT', 'true'); ?><b><?php echo TEXT_PERSIST; ?></b><br /><?php echo TEXT_PERSIST_LONG; ?></p>
<p><?php echo os_draw_radio_field_installer('STORE_SESSIONS', 'files', true); ?><b><?php echo TEXT_SESS_FILE; ?></b><br /><?php echo os_draw_radio_field_installer('STORE_SESSIONS', 'mysql',false); ?><b><?php echo TEXT_SESS_DB; ?></b><br /><?php echo TEXT_SESS_LONG; ?></p>
</fieldset>


<input type="hidden" name="install[]" value="configure" />
<input type="hidden" name="task" value="" />

</form>

<?php
  }
?>  			

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
		<div id="footer1">
			<div id="footer2">
				<div id="footer3"></div>
			</div>
		</div>
		
<?php echo _copy(); ?>
</body>
</html>