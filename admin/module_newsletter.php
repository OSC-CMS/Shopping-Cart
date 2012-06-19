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
  require_once(_FUNC_ADMIN.'wysiwyg_tiny.php');
  require_once(_LIB.'phpmailer/class.phpmailer.php');

  switch (@$_GET['action']) {

    case 'save': 

     $id=os_db_prepare_input((int)$_POST['ID']);
     $status_all=os_db_prepare_input(@$_POST['status_all']);
     if (@$newsletter_title=='') $newsletter_title='no title';
     $customers_status=os_get_customers_statuses();
     
     $rzp='';
     for ($i=0,$n=sizeof($customers_status);$i<$n; $i++) {
         if (os_db_prepare_input(@$_POST['status'][$i])=='yes') {
             if ($rzp!='') $rzp.=',';
             $rzp.=$customers_status[$i]['id'];
         }
     }
     
      if (os_db_prepare_input(@$_POST['status_all'])=='yes') $rzp.=',all';

   $error=false;
   if ($error == false) {

      $sql_data_array = array( 'title'=> os_db_prepare_input($_POST['title']),
                               'status' => '0',
                               'bc'=>$rzp,
                               'cc'=>os_db_prepare_input($_POST['cc']),
                               'date' => 'now()',
                               'body' => os_db_prepare_input($_POST['newsletter_body']));

   if ($id!='') {
   os_db_perform(TABLE_MODULE_NEWSLETTER, $sql_data_array, 'update', "newsletter_id = '" . $id . "'");
   os_db_query("DROP TABLE IF EXISTS ".TABLE_NEWSLETTER_TEMP.$id);
   os_db_query("CREATE TABLE ".TABLE_NEWSLETTER_TEMP.$id."
                  (
                     id int(11) NOT NULL auto_increment,
                    customers_id int(11) NOT NULL default '0',
                    customers_status int(11) NOT NULL default '0',
                    customers_firstname varchar(64) NOT NULL default '',
                    customers_lastname varchar(64) NOT NULL default '',
                    customers_email_address text NOT NULL,
                    mail_key varchar(32) NOT NULL,
                    date datetime NOT NULL default '0000-00-00 00:00:00',
                    comment varchar(64) NOT NULL default '',
                    PRIMARY KEY  (id)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");
   } else {
   os_db_perform(TABLE_MODULE_NEWSLETTER, $sql_data_array);
   $id=os_db_insert_id();
   os_db_query("DROP TABLE IF EXISTS ".TABLE_NEWSLETTER_TEMP.$id);
   os_db_query("CREATE TABLE ".TABLE_NEWSLETTER_TEMP.$id."
                  (
                     id int(11) NOT NULL auto_increment,
                    customers_id int(11) NOT NULL default '0',
                    customers_status int(11) NOT NULL default '0',
                    customers_firstname varchar(64) NOT NULL default '',
                    customers_lastname varchar(64) NOT NULL default '',
                    customers_email_address text NOT NULL,
                    mail_key varchar(32) NOT NULL,
                    date datetime NOT NULL default '0000-00-00 00:00:00',
                    comment varchar(64) NOT NULL default '',
                    PRIMARY KEY  (id)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");
   }

   $flag='';
   if (!strpos($rzp,'all')) $flag='true';
   $rzp=str_replace(',all','',$rzp);
   $groups=explode(',',$rzp);
   $sql_data_array='';

   for ($i=0,$n=sizeof($groups);$i<$n;$i++) {
   
   if (os_db_prepare_input(@$_POST['status_all'])=='yes') {
   $customers_query=os_db_query("SELECT
                                  customers_id,
                                  customers_firstname,
                                  customers_lastname,
                                  customers_email_address
                                  FROM ".TABLE_CUSTOMERS."
                                  WHERE
                                  customers_status='".$groups[$i]."'");
   } else {
      $customers_query=os_db_query("SELECT
                                  customers_email_address,
                                  customers_id,
                                  customers_firstname,
                                  customers_lastname,
                                  mail_key        
                                  FROM ".TABLE_NEWSLETTER_RECIPIENTS."
                                  WHERE
                                  customers_status='".$groups[$i]."' and
                                  mail_status='1'");
   }
   while ($customers_data=os_db_fetch_array($customers_query)){
          $sql_data_array=array(
                               'customers_id'=>$customers_data['customers_id'],
                               'customers_status'=>$groups[$i],
                               'customers_firstname'=>$customers_data['customers_firstname'],
                               'customers_lastname'=>$customers_data['customers_lastname'],
                               'customers_email_address'=>$customers_data['customers_email_address'],
                               'mail_key'=>$customers_data['mail_key'],
                               'date'=>'now()');

   os_db_perform(TABLE_NEWSLETTER_TEMP.$id, $sql_data_array);
   }


   }

   os_redirect(os_href_link(FILENAME_MODULE_NEWSLETTER));
   }

   break;

   case 'delete':

   os_db_query("DELETE FROM ".TABLE_MODULE_NEWSLETTER." WHERE   newsletter_id='".(int)$_GET['ID']."'");
   os_redirect(os_href_link(FILENAME_MODULE_NEWSLETTER));

   break;

   case 'send':
   $package_size='30';
   os_redirect(os_href_link(FILENAME_MODULE_NEWSLETTER,'send=0,'.$package_size.'&ID='.(int)$_GET['ID']));
   }

if (@$_GET['send']) {

$limits=explode(',',$_GET['send']);
$limit_low = $limits['0'];
$limit_up = $limits['1'];



     $limit_query=os_db_query("SELECT count(*) as count
                                FROM ".TABLE_NEWSLETTER_TEMP.(int)$_GET['ID']."
                                ");
     $limit_data=os_db_fetch_array($limit_query);


    $email_query=os_db_query("SELECT
                               customers_firstname,
                               customers_lastname,
                               customers_email_address,
                               mail_key ,
                               id
                               FROM  ".TABLE_NEWSLETTER_TEMP.(int)$_GET['ID']."
                               LIMIT ".$limit_low.",".$limit_up);

     $email_data=array();
 while ($email_query_data=os_db_fetch_array($email_query)) {

 $email_data[]=array('id' => $email_query_data['id'],
                      'firstname'=>$email_query_data['customers_firstname'],
                      'lastname'=>$email_query_data['customers_lastname'],
                      'email'=>$email_query_data['customers_email_address'],
                      'key'=>$email_query_data['mail_key']);
 }

 $package_size='30';
 $break='0';
 if ($limit_data['count']<$limit_up) {
     $limit_up=$limit_data['count'];
     $break='1';
 }
 $max_runtime=$limit_up-$limit_low;
  $newsletters_query=os_db_query("SELECT
                                   title,
                                    body,
                                    bc,
                                    cc
                                  FROM ".TABLE_MODULE_NEWSLETTER."
                                  WHERE  newsletter_id='".(int)$_GET['ID']."'");
 $newsletters_data=os_db_fetch_array($newsletters_query);


 for ($i=1;$i<=$max_runtime;$i++)
 {

 $link1 = chr(13).chr(10).chr(13).chr(10).TEXT_NEWSLETTER_REMOVE.chr(13).chr(10).chr(13).chr(10).HTTP_CATALOG_SERVER.DIR_WS_CATALOG.FILENAME_CATALOG_NEWSLETTER.'?action=remove&email='.$email_data[$i-1]['email'].'&key='.$email_data[$i-1]['key'];

 $link2 = $link2 = '<br /><br /><hr>'.TEXT_NEWSLETTER_REMOVE.'<br /><a href="'.HTTP_CATALOG_SERVER.DIR_WS_CATALOG.FILENAME_CATALOG_NEWSLETTER.'?action=remove&email='.$email_data[$i-1]['email'].'&key='.$email_data[$i-1]['key'].'">' . TEXT_REMOVE_LINK . '</a>';


  os_php_mail(EMAIL_SUPPORT_ADDRESS,
               EMAIL_SUPPORT_NAME,
               $email_data[$i-1]['email'] ,
               $email_data[$i-1]['lastname'] . ' ' . $email_data[$i-1]['firstname'] ,
               '',
               EMAIL_SUPPORT_REPLY_ADDRESS,
               EMAIL_SUPPORT_REPLY_ADDRESS_NAME,
                '',
                '',
                $newsletters_data['title'],
                $newsletters_data['body'].$link2,
                $newsletters_data['body'].$link1);

  os_db_query("UPDATE ".TABLE_NEWSLETTER_TEMP.(int)$_GET['ID']." SET comment='send' WHERE id='".$email_data[$i-1]['id']."'");

 }
 if ($break=='1') {

          $limit1_query=os_db_query("SELECT count(*) as count
                                FROM ".TABLE_NEWSLETTER_TEMP.(int)$_GET['ID']."
                                WHERE comment='send'");
     $limit1_data=os_db_fetch_array($limit1_query);

     if ($limit1_data['count']-$limit_data['count']<=0)
     {
     os_db_query("UPDATE ".TABLE_MODULE_NEWSLETTER." SET status='1' WHERE newsletter_id='".(int)$_GET['ID']."'");
     os_redirect(os_href_link(FILENAME_MODULE_NEWSLETTER));
     } else {
     echo '<b>'.$limit1_data['count'].'<b> emails send<br />';
     echo '<b>'.$limit1_data['count']-$limit_data['count'].'<b> emails left';
     }


 } else {
 $limit_low=$limit_up+1;
 $limit_up=$limit_low+$package_size;
 os_redirect(os_href_link(FILENAME_MODULE_NEWSLETTER,'send='.$limit_low.','.$limit_up.'&ID='.(int)$_GET['ID']));
 }


}

add_action ('head_admin', 'head_newsletter');

function head_newsletter ()
{  
   $query=os_db_query("SELECT code FROM ". TABLE_LANGUAGES ." WHERE languages_id='".$_SESSION['languages_id']."'");
   $data=os_db_fetch_array($query);
   if (@$_GET['action']!='') echo os_wysiwyg_tiny('mail',$data['code']); 
}
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('plugin.gif',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
 <?php
 if (@$_GET['send'])
 {
 ?>

      <tr><td>
      Sending
      </td></tr>
<?php
}
?>

      <tr>
        <td><table width="100%" border="0">
          <tr>
            <td>
 <?php

switch (@$_GET['action']) {

    default:

 $customer_group_query=os_db_query("SELECT
                                     customers_status_name,
                                     customers_status_id,
                                     customers_status_image
                                     FROM ".TABLE_CUSTOMERS_STATUS."
                                     WHERE
                                     language_id='".$_SESSION['languages_id']."'");
 $customer_group=array();
 while ($customer_group_data=os_db_fetch_array($customer_group_query)) {

     $group_query=os_db_query("SELECT count(*) as count
                                FROM ".TABLE_NEWSLETTER_RECIPIENTS."
                                WHERE mail_status='1' and
                                customers_status='".$customer_group_data['customers_status_id']."'");
     $group_data=os_db_fetch_array($group_query);


 $customer_group[]=array( 'ID'=>$customer_group_data['customers_status_id'],
                          'NAME'=>$customer_group_data['customers_status_name'],
                          'IMAGE'=>$customer_group_data['customers_status_image'],
                          'USERS'=>$group_data['count']);


 }

 ?>
<br />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table border="0" width="100%" cellspacing="2" cellpadding="2">
        <tr class="dataTableHeadingRow">
          <td class="dataTableHeadingContent" width="150" ><?php echo TITLE_CUSTOMERS; ?></td>
          <td class="dataTableHeadingContent"  ><?php echo TITLE_STK; ?></td>
        </tr>

        <?php
for ($i=0,$n=sizeof($customer_group); $i<$n; $i++) {
?>
        <tr>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" valign="middle" align="left"><?php echo os_image(http_path('icons_admin') . $customer_group[$i]['IMAGE'], ''); ?><?php echo $customer_group[$i]['NAME']; ?></td>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left"><?php echo $customer_group[$i]['USERS']; ?></td>
        </tr>
        <?php
}
?>
      </table></td>
    <td width="30%" align="right" valign="top""><?php
    echo '<a class="button" href="'.os_href_link(FILENAME_MODULE_NEWSLETTER,'action=new').'"><span>'.BUTTON_NEW_NEWSLETTER.'</span></a>';


    ?></td>
  </tr>
</table>
 <br />
 <?php

 $newsletters_query=os_db_query("SELECT
                                   newsletter_id,date,title
                                  FROM ".TABLE_MODULE_NEWSLETTER."
                                  WHERE status='0'");
 $news_data=array();
 while ($newsletters_data=os_db_fetch_array($newsletters_query)) {

 $news_data[]=array(    'id' => $newsletters_data['newsletter_id'],
                        'date'=>$newsletters_data['date'],
                        'title'=>$newsletters_data['title']);
 }

?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
        <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" width="30" ><?php echo TITLE_DATE; ?></td>
          <td class="dataTableHeadingContent" width="80%" ><?php echo TITLE_NOT_SEND; ?></td>
          <td class="dataTableHeadingContent"  >.</td>
        </tr>
<?php
for ($i=0,$n=sizeof($news_data); $i<$n; $i++) {
if ($news_data[$i]['id']!='') {
?>
        <tr>
        <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left"><?php echo $news_data[$i]['date']; ?></td>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" valign="middle" align="left"><?php echo os_image(DIR_WS_CATALOG.'images/icons/arrow.gif'); ?><a href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'ID='.$news_data[$i]['id']); ?>"><b><?php echo $news_data[$i]['title']; ?></b></a></td>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left">

          </td>
        </tr>
 <?php

if (@$_GET['ID']!='' && @$_GET['ID']==@$news_data[$i]['id']) {

$total_query=os_db_query("SELECT
                           count(*) as count
                           FROM ".TABLE_NEWSLETTER_TEMP.(int)$_GET['ID']."");
$total_data=os_db_fetch_array($total_query);
?>
<tr>
<td class="dataTableContent_products" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left"></td>
<td colspan="2" class="dataTableContent_products" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left"><?php echo TEXT_SEND_TO.$total_data['count']; ?></td>
</tr>
<td class="dataTableContent" valign="top" style="border-bottom: 1px solid; border-color: #999999;" align="left">
  <a class="button" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=delete&ID='.$news_data[$i]['id']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')"><span><?php echo BUTTON_DELETE.'</span></a><br />'; ?>
  <a class="button" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=edit&ID='.$news_data[$i]['id']); ?>"><span><?php echo BUTTON_EDIT.'</span></a>'; ?>
  <br /><br /><div style="height: 1px; background: Black; margin: 3px 0;"></div>
  <a class="button" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=send&ID='.$news_data[$i]['id']); ?>"><span><?php echo BUTTON_SEND.'</span></a>'; ?>

</td>
<td colspan="2" class="dataTableContent" style="border-bottom: 1px solid; border-color: #999999; text-align: left;">
<?php
    $newsletters_query=os_db_query("SELECT
                                   title,body,cc,bc
                                  FROM ".TABLE_MODULE_NEWSLETTER."
                                  WHERE newsletter_id='".(int)$_GET['ID']."'");
   $newsletters_data=os_db_fetch_array($newsletters_query);

echo TEXT_TITLE.$newsletters_data['title'].'<br />';

     $customers_status=os_get_customers_statuses();
     for ($i=0,$n=sizeof($customers_status);$i<$n; $i++) {

     $newsletters_data['bc']=str_replace($customers_status[$i]['id'],$customers_status[$i]['text'],$newsletters_data['bc']);

     }

echo TEXT_TO.$newsletters_data['bc'].'<br />';
echo TEXT_CC.$newsletters_data['cc'].'<br /><br />'.TEXT_PREVIEW;
echo '<table style="border-color: #cccccc; border: 1px solid;" width="100%"><tr><td>'.$newsletters_data['body'].'</td></tr></table>';
?>
</td></tr>
<?php
}
?>

<?php
}
}


?>
</table>
<br /><br />
<?php
 $newsletters_query=os_db_query("SELECT
                                   newsletter_id,date,title
                                  FROM ".TABLE_MODULE_NEWSLETTER."
                                  WHERE status='1'");
 $news_data=array();
 while ($newsletters_data=os_db_fetch_array($newsletters_query)) {

 $news_data[]=array(    'id' => $newsletters_data['newsletter_id'],
                        'date'=>$newsletters_data['date'],
                        'title'=>$newsletters_data['title']);
 }

?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
        <tr class="dataTableHeadingRow">
          <td class="dataTableHeadingContent" width="80%" ><?php echo TITLE_SEND; ?></td>
          <td class="dataTableHeadingContent"><?php echo TITLE_ACTION; ?></td>
        </tr>
<?php
for ($i=0,$n=sizeof($news_data); $i<$n; $i++) {
if ($news_data[$i]['id']!='') {
?>
        <tr>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" valign="middle" align="left"><?php echo $news_data[$i]['date'].'    '; ?><b><?php echo $news_data[$i]['title']; ?></b></td>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left">

  <a href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=delete&ID='.$news_data[$i]['id']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
  <?php
  echo os_image(http_path('icons_admin').'delete.gif','Delete','','','style="cursor:pointer" onClick="return confirm(\''.DELETE_ENTRY.'\')"').'  '.TEXT_DELETE.'</a>&nbsp;&nbsp;';
  ?>
<a href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=edit&ID='.$news_data[$i]['id']); ?>">
<?php echo os_image(http_path('icons_admin').'icon_edit.gif','Edit','','').'  '.TEXT_EDIT.'</a>'; ?>





          </td>
        </tr>
<?php
}
}


?>
</table>

<?php


  break;       // end default page

  case 'edit':

   $newsletters_query=os_db_query("SELECT title,body,cc,bc FROM ".TABLE_MODULE_NEWSLETTER." WHERE newsletter_id='".(int)$_GET['ID']."'");
   $newsletters_data=os_db_fetch_array($newsletters_query);

  case 'safe':
  case 'new':  // action for NEW newsletter!

$customers_status=os_get_customers_statuses();


  echo os_draw_form('edit_newsletter',FILENAME_MODULE_NEWSLETTER,'action=save','post','enctype="multipart/form-data"').os_draw_hidden_field('ID',@$_GET['ID']);
  ?>

  <br /><br />
 <table class="main" width="100%" border="0">
   </tr>
      <tr>
      <td width="10%"><?php echo TEXT_TITLE; ?></td>
      <td width="90%"><?php echo os_draw_input_field('title',@$newsletters_data['title'],'size=100'); ?></td>
   </tr>
   <tr>
      <td width="10%"><?php echo TEXT_TO; ?></td>
      <td width="90%"><?php
for ($i=0,$n=sizeof($customers_status);$i<$n; $i++) {

     $group_query=os_db_query("SELECT count(*) as count
                                FROM ".TABLE_NEWSLETTER_RECIPIENTS."
                                WHERE mail_status='1' and
                                customers_status='".$customers_status[$i]['id']."'");
     $group_data=os_db_fetch_array($group_query);

     $group_query=os_db_query("SELECT count(*) as count
                                FROM ".TABLE_CUSTOMERS."
                                WHERE
                                customers_status='".$customers_status[$i]['id']."'");
     $group_data_all=os_db_fetch_array($group_query);

     $bc_array = explode(',', @$newsletters_data['bc']);

echo os_draw_checkbox_field('status['.$i.']','yes', in_array($customers_status[$i]['id'], $bc_array)).' '.$customers_status[$i]['text'].'  <i>(<b>'.$group_data['count'].'</b> '.TEXT_USERS.$group_data_all['count'].TEXT_CUSTOMERS.'<br />';

}
echo os_draw_checkbox_field('status_all', 'yes',in_array('all', $bc_array)).' <b>'.TEXT_NEWSLETTER_ONLY.'</b>';

       ?></td>
   </tr>
         <tr>
      <td width="10%"><?php echo TEXT_CC; ?></td>
      <td width="90%"><?php

       echo os_draw_input_field('cc',@$newsletters_data['cc'],'size=100'); ?></td>
   </tr>
      </tr>
      <tr>
      <td width="10%" valign="top"><?php echo TEXT_BODY; ?></td>
      <td width="90%"><?php

echo os_draw_textarea_field('newsletter_body', 'soft', '103', '25', stripslashes(@$newsletters_data['body']));

 ?><br /><a href="javascript:toggleHTMLEditor('newsletter_body');" class="code"><?php echo TEXT_EDIT_E;?></a></td>
   </tr>
   </table>
   <a class="button" onClick="this.blur();" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER); ?>"><span><?php echo BUTTON_BACK; ?></span></a>
   <right><?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>'; ?></right>
  </form>
  <?php

  break;
} // end switch
?>


</td>

          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>

</body>
</html>
<?php $main->bottom(); ?>