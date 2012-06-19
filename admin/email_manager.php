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
  
  add_action('head_admin', 'head_email_manager');
  
  function head_email_manager()
  {
    _e('<script type="text/javascript" src="includes/javascript/tabber.js"></script>');
    _e('<link rel="stylesheet" href="includes/javascript/tabber.css" TYPE="text/css" MEDIA="screen">');
    _e('<link rel="stylesheet" href="includes/javascript/tabber-print.css" TYPE="text/css" MEDIA="print">');
  }
?>

<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">

    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
      

<br />
<?php echo TEXT_CATALOG_TEMPLATES; ?>
<br />

<?php

$path_parts = pathinfo($_GET['file']);

$file = _MAIL. $_SESSION['language_admin'] . '/' . $path_parts['basename'];

if (is_writable($file)) 
{
  $chmod = '<font color="Green">' . TEXT_YES . '</font>';
}
else
{
  $chmod = '<font color="Red">' . TEXT_NO . '</font>';
}
$st = 1;
if (!empty($_GET['file'])) {
$st =2;
if(file_exists($file)) {
	$code = file_get_contents($file);
}else{
  $code = TEXT_FILE_SELECT;
}
}
?>
<?php echo os_draw_form('select', FILENAME_EMAIL_MANAGER, '', 'get'); ?>

<?php

$file_list = os_array_merge(array('0' => array('id' => '', 'text' => SELECT_FILE)),os_getFiles(_MAIL.$_SESSION['language_admin'] . '/',array('.txt','.html')));

echo os_draw_pull_down_menu('file',$file_list,$_REQUEST['file']);

echo '&nbsp;<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_EDIT . '"/>' . BUTTON_EDIT . '</button></span>';

               
?>
<br /><br />
</form>
<?php echo os_draw_form('edit', FILENAME_EMAIL_MANAGER, os_get_all_get_params(), 'post'); ?>

<?php if($_POST['save'] && is_file($file)){ ?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">

<tr>
    <td>

<?php echo TEXT_FILE_SAVED; ?>
<br />

    </td>
</tr>

</table>

<?php } else { ?>

<?php if (isset($_GET['file'])) { ?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
    <td valign="top">

		  <?php echo TEXT_FILE; ?> <b><?php echo $file ?></b><br /><?php echo TEXT_FILE_WRITABLE; ?> <b><?php echo $chmod ?></b><br />

      <textarea name="code" rows="20" cols="80">
      <?php echo $code ?>
      </textarea>

<br /><br />      
      
      <?php 
  if (is_writable($file)) 
  {
	 echo '<span class="button"><button type="submit" name="save" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>'; 
  }
  ?>
        
    </td>
</tr>

</table>

<?php } ?>

<?php } ?>

<?

if($_POST['save'] && is_file($file)){

if (is_writable($file)) {

    if (!$handle = fopen($file, 'w')) {
         echo TEXT_FILE_OPEN_ERROR . " ($file)";
         exit;
    }

    if (fwrite($handle, stripslashes($_POST['code'])) === FALSE) {
        echo TEXT_FILE_WRITE_ERROR . " ($file)";
        exit;
    }
    
//    echo TEXT_FILE_WRITE_SUCCESS;
    
    fclose($handle);

} else {
    echo TEXT_FILE_PERMISSION_ERROR;
}

}
?>

<br /><br />

<?php if($_POST['save'] && is_file($file)){ ?>

<a class="button" onClick="this.blur();" href="<?php echo os_href_link(FILENAME_EMAIL_MANAGER); ?>"><span><?php echo BUTTON_BACK; ?></span></a>

<?php } ?>

</form>     

<br />
<?php echo TEXT_ADMIN_TEMPLATES; ?>
<br />

<?php

$path_parts_admin = pathinfo($_GET['file_admin']);

$file_admin = _MAIL.'admin/' . $_SESSION['language_admin'] . '/' . $path_parts_admin['basename'];

if (is_writable($file_admin)) {
  $chmod_admin = '<font color="Green">' . TEXT_YES . '</font>';
}else{
  $chmod_admin = '<font color="Red">' . TEXT_NO . '</font>';
}


if ((!empty($_GET['file_admin'])) and ($st ==1)) 
{
if(file_exists($file_admin)) {
	$code_admin = file_get_contents($file_admin);
}else{
  $code_admin = TEXT_FILE_SELECT;
}
}
?>
<?php echo os_draw_form('select_admin', FILENAME_EMAIL_MANAGER, '', 'get'); ?>

<?php

$file_list_admin = os_array_merge(array('0' => array('id' => '', 'text' => SELECT_FILE)),os_getFiles(_MAIL.'admin/' . $_SESSION['language_admin'] . '/',array('.txt','.html')));

echo os_draw_pull_down_menu('file_admin',$file_list_admin,$_REQUEST['file_admin']);

echo '&nbsp;<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_EDIT . '"/>' . BUTTON_EDIT . '</button></span>';

               
?>
<br /><br />
</form>
<?php echo os_draw_form('edit_admin', FILENAME_EMAIL_MANAGER, os_get_all_get_params(), 'post'); ?>

<?php if($_POST['save'] && is_file($file_admin)){ ?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">

<tr>
    <td>

<?php echo TEXT_FILE_SAVED; ?>
<br />

    </td>
</tr>

</table>

<?php } else { ?>

<?php if (isset($_GET['file_admin'])) { ?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
    <td valign="top">

		  <?php echo TEXT_FILE; ?> <b><?php echo $file_admin ?></b><br /><?php echo TEXT_FILE_WRITABLE; ?> <b><?php echo $chmod_admin ?></b><br />

      <textarea name="code_admin" rows="20" cols="80">
      <?php echo $code_admin ?>
      </textarea>

<br /><br />      
      
      <?php 
  if (is_writable($file_admin)) {
	echo '<span class="button"><button type="submit" name="save" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>'; 
  }
  ?>
        
    </td>
</tr>

</table>

<?php } ?>

<?php } ?>

<?

if($_POST['save'] && is_file($file_admin)){

if (is_writable($file_admin)) {

    if (!$handle = fopen($file_admin, 'w')) {
         echo TEXT_FILE_OPEN_ERROR . " ($file_admin)";
         exit;
    }

    if (fwrite($handle, stripslashes($_POST['code_admin'])) === FALSE) {
        echo TEXT_FILE_WRITE_ERROR . " ($file_admin)";
        exit;
    }
    
//    echo TEXT_FILE_WRITE_SUCCESS;
    
    fclose($handle);

} else {
    echo TEXT_FILE_PERMISSION_ERROR;
}

}
?>

<br /><br />

<?php if($_POST['save'] && is_file($file_admin)){ ?>

<a class="button" onClick="this.blur();" href="<?php echo os_href_link(FILENAME_EMAIL_MANAGER); ?>"><span><?php echo BUTTON_BACK; ?></span></a>

<?php } ?>

</form>
          </td>
      </tr>
    </table>
<?php $main->bottom(); ?>