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

if ($_GET['action']) 
{
    switch ($_GET['action'])
	{
      case 'save':

       $admin_access_query = os_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int)$_GET['cID'] . "'");
       $admin_access = os_db_fetch_array($admin_access_query);

       $fields = mysql_list_fields(DB_DATABASE, TABLE_ADMIN_ACCESS);
       $columns = mysql_num_fields($fields);

		for ($i = 0; $i < $columns; $i++) 
		{
             $field=mysql_field_name($fields, $i);
                    if ($field!='customers_id') 
					{

                    os_db_query("UPDATE ".TABLE_ADMIN_ACCESS." SET
                                  ".$field."=0 where customers_id='".(int)$_GET['cID']."'");
    		        }
        }



      $access_ids='';
        if(isset($_POST['access'])) foreach($_POST['access'] as $key){

        os_db_query("UPDATE ".TABLE_ADMIN_ACCESS." SET ".$key."=1 where customers_id='".(int)$_GET['cID']."'");

        }

        os_redirect(os_href_link(FILENAME_CUSTOMERS, 'cID=' . (int)$_GET['cID'], 'NONSSL'));
        break;
      }
    }
    if ($_GET['cID'] != '') {
      if ($_GET['cID'] == 1) {
        os_redirect(os_href_link(FILENAME_CUSTOMERS, 'cID=' . (int)$_GET['cID'], 'NONSSL'));
      } else {
        $allow_edit_query = os_db_query("select customers_status, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$_GET['cID'] . "'");
        $allow_edit = os_db_fetch_array($allow_edit_query);
        if ($allow_edit['customers_status'] != 0 || $allow_edit == '') {
          os_redirect(os_href_link(FILENAME_CUSTOMERS, 'cID=' . (int)$_GET['cID'], 'NONSSL'));
        }
      }
    }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main">
          <?php os_header('portfolio_package.gif',TEXT_ACCOUNTING.' '.$allow_edit['customers_lastname'].' '.$allow_edit['customers_firstname']); ?> 

    
     <br /><?php echo TXT_GROUPS; ?><br />

      <table width="100%" cellpadding="0" cellspacing="2">
      <tr>
       <td class="dataTableHeadingContentAccounting" bgcolor="FF6969" >&nbsp;&nbsp;&nbsp;&nbsp;</td>
       <td width="100%" class="main"><?php echo TXT_SYSTEM; ?></td>
      </tr>
      <tr>
       <td class="dataTableHeadingContentAccounting" width="30" bgcolor="69CDFF" >&nbsp;&nbsp;&nbsp;&nbsp;</td>
       <td width="100%" class="main"><?php echo TXT_PRODUCTS; ?></td>
      </tr>
      <tr>
       <td class="dataTableHeadingContentAccounting" width="30" bgcolor="6BFF7F" >&nbsp;&nbsp;&nbsp;&nbsp;</td>
       <td width="100%" class="main"><?php echo TXT_CUSTOMERS; ?></td>
      </tr>
      <tr>
       <td class="dataTableHeadingContentAccounting" width="10" bgcolor="BFA8FF" >&nbsp;&nbsp;&nbsp;&nbsp;</td>
       <td width="100%" class="main"><?php echo TXT_STATISTICS; ?></td>
      </tr>
      <tr>
       <td class="dataTableHeadingContentAccounting" width="10" bgcolor="FFE6A8" >&nbsp;&nbsp;&nbsp;&nbsp;</td>
       <td width="100%" class="main"><?php echo TXT_TOOLS; ?></td>
      </tr>
      </table>
      <br />
      </td>
      </tr>
      <tr><table border="0" cellpadding="2" cellspacing="2" width="100%">
          <tr class="dataTableHeadingRow">
		  <td class="dataTableHeadingContent" width="30px"><input type="checkbox" onClick="javascript:SwitchCheckAccounting();"></td>
		  <td class="dataTableHeadingContent" width="60px">&nbsp;&nbsp;&nbsp;&nbsp;</td>
            
            <td class="dataTableHeadingContent"><?php echo TEXT_ACCESS; ?></td>
            <td class="dataTableHeadingContent"><?php echo TEXT_ALLOWED; ?></td>
          </tr>
<?php
 echo os_draw_form('accounting', FILENAME_ACCOUNTING, 'cID=' . $_GET['cID']  . '&action=save', 'post', 'enctype="multipart/form-data"');

   $admin_access='';
    $customers_id = os_db_prepare_input($_GET['cID']);
    $admin_access_query = os_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int)$_GET['cID'] . "'");
    $admin_access = os_db_fetch_array($admin_access_query);

    $group_query=os_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = 'groups'");
    $group_access = os_db_fetch_array($group_query);
    if ($admin_access == '') {
      os_db_query("INSERT INTO " . TABLE_ADMIN_ACCESS . " (customers_id) VALUES ('" . (int)$_GET['cID'] . "')");
      $admin_access_query = os_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int)$_GET['cID'] . "'");
      $group_query=os_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = 'groups'");
      $group_access = os_db_fetch_array($admin_access_query);
      $admin_access = os_db_fetch_array($admin_access_query);
    }

$fields = mysql_list_fields(DB_DATABASE, TABLE_ADMIN_ACCESS);
$columns = mysql_num_fields($fields);

$color = '';
for ($i = 0; $i < $columns; $i++) {
    $field=mysql_field_name($fields, $i);
    if ($field!='customers_id') {
    $checked='';
    if ($admin_access[$field] == '1') $checked='checked';

    // colors
    switch ($group_access[$field]) {
            case '1':
            $color='#FF6969';
            break;
            case '2':
            $color='#69CDFF';
            break;
            case '3':
            $color='#6BFF7F';
            break;
            case '4':
            $color='#BFA8FF';
            break;
            case '5':
            $color='#FFE6A8';

    }
	
    $access_name = '';
	$access_name_c = 'ACCESS_'.strtoupper($field); //constant()
	
	if ( defined($access_name_c) )
	{
	   $access_name = constant($access_name_c);
	}
	
    echo '<tr >
	<td><input type="checkbox" name="access[]" value="'.$field.'"'.$checked.'></td>
    <td class="dataTableHeadingContentAccounting" style="background-color: '.$color.';	border: 1px solid '.$color.';  " >&nbsp;</td>
	
        <td>
        <b>'.$field.'</b> </td>
        <td width="100%"><i><small>'.$access_name.'</small></i></td></tr>';
    }
}
?>
    </table>
<span class="button"><button  type="submit"  onClick="return confirm('<?php echo SAVE_ENTRY; ?>')" value="<?php echo BUTTON_SAVE; ?>"><?php echo BUTTON_SAVE; ?></button></span>
</td>
  </tr>
</table>
<?php $main->bottom(); ?>