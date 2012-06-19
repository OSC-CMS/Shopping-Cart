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

require ('includes/top.php');
$osTemplate = new osTemplate;

add_action('head_admin', 'head_tabs');

?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
<tr>
<td class="boxCenter" width="100%" valign="top">
<?php  $main->heading('portfolio_package.gif', HEADING_TITLE); ?> 

<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
<td width="70%" valign=top>

		<div id="tabs">
			<ul>
				<li><a href="#main"><?php echo  TEXT_MAIN_MENU; ?></a></li>
				<li><a href="#orders"><?php echo TEXT_SUMMARY_ORDERS; ?></a></li>
				<li><a href="#customers"><?php echo TEXT_SUMMARY_CUSTOMERS; ?></a></li>
				<li><a href="#products"><?php echo TEXT_SUMMARY_PRODUCTS; ?></a></li>
			</ul>
			<div id="main">
			<?php include(get_path('page_admin') . 'index/main.php'); ?>
			</div>
			<div id="orders">
			<?php include(get_path('page_admin') . 'index/orders.php'); ?>
			</div>
			<div id="customers">
			<?php include(get_path('page_admin') . 'index/customers.php'); ?>
			</div>
			<div id="products">
			<?php include(get_path('page_admin') . 'index/products.php'); ?>
			</div>
		</div>



 </td>
 
<td valign="top" style="padding-left:5px; width:10cm;">
<div class="tabber" >
<?php 
$system_error = 1;
$file_warning = '';
$folder_warning = '';
$installed_payment = '';
$installed_shipping = '';

include(get_path('modules_admin') . FILENAME_SECURITY_CHECK); 
	?>				  

		<div id="tabs2">
			<ul>
			<?php if ($system_error == 1) {?>	<li><a href="#warning"><?php echo  MENU_SYSTEM_ERROR; ?></a></li><?php }?>
			<li><a href="#cache"><?php echo  TEXT_CACHE; ?></a></li>
			<li><a href="#themes"><?php echo  TEXT_THEMES; ?></a></li>
			</ul>
			<?php if ($system_error == 1) {?>
			<div id="warning">
	       <?php include(get_path('page_admin') . 'index/warning.php'); ?>
			</div><?php }?>
			
			<div id="cache">
			  <?php  include(get_path('page_admin') . 'index/cache.php'); ?>
			</div>
			
			<div id="themes">
			<?php include(get_path('page_admin') . 'index/themes.php'); ?>
			</div>

		</div>

</div>    

</td>
</tr>
</table>	

</td>
  </tr>
</table>
<?php $main->bottom(); ?>