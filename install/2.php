<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
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
if (isset($_POST['DB_PREFIXX']) && !empty($_POST['DB_PREFIXX']))
{
    $_SESSION['DB_PREFIX'] = $_POST['DB_PREFIXX'].'_';
}
else
{
    $_SESSION['DB_PREFIX'] = $_POST['DB_PREFIXX'];
}

include('lang/'.$_SESSION['language'].'/lang.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr" >
<head>
<meta http-equiv=Content-Type content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE_STEP2; ?></title>
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
<div class="step-off"><?php echo STEP1; ?></div>
<div class="step-on"><?php echo STEP2; ?></div>
<div class="step-off"><?php echo STEP3; ?></div>
<div class="step-off"><?php echo STEP4; ?></div>
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
				<span class="step"><?php echo TITLE_STEP2; ?></span>
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


				
				
				
				
<?php echo TEXT_WELCOME_STEP2; ?>
<?php
if (os_in_array('database', $_POST['install']))
{   
   $db = array();
   $db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
   $db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
   $db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
   $db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));
   $db_error = false;  
   os_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);

   if (!$db_error) 
   {
      db_test_create_db_permission($db['DB_DATABASE']);
   }
    
   if ($db_error) 
   {
?>

<p>
<?php echo TEXT_CONNECTION_ERROR; ?>
</p>

<p>
<?php echo TEXT_DB_ERROR; ?>
</p>

<div class="contacterror">

<b><?php echo $db_error; ?></b>

</div>

<p><?php echo TEXT_DB_ERROR_1; ?></p>
<p><?php echo TEXT_DB_ERROR_2; ?></p>

<form name="install" action="1.php" method="post">

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

</form>

<?php
    } else {
?>


<div class='os-ok-content'><?php echo TEXT_CONNECTION_SUCCESS; ?></div>

<p><?php echo TEXT_PROCESS_1; ?></p>
<p><?php echo TEXT_PROCESS_2; ?></p>


<form name="install" action="3.php" method="post">
<table border="0"><tr><td>
<input type="checkbox" name="OS_TEST_BASE" id="OS_TEST_BASE"></td><td><label for="OS_TEST_BASE"><?php echo STEP2_TEST;?></label></td></tr></table>
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
	  
}

}	  
?>

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
