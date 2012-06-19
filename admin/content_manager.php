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
 
  set_content_url_cache(); 
  
 $languages = os_get_languages();

 
 if ($_GET['special']=='delete') {
 
 os_db_query("DELETE FROM ".TABLE_CONTENT_MANAGER." where content_id='".(int)$_GET['coID']."'");
 os_redirect(os_href_link(FILENAME_CONTENT_MANAGER));
} 

 if ($_GET['special']=='delete_product') {
 
 os_db_query("DELETE FROM ".TABLE_PRODUCTS_CONTENT." where content_id='".(int)$_GET['coID']."'");
 os_redirect(os_href_link(FILENAME_CONTENT_MANAGER,'pID='.(int)$_GET['pID']));
} 


if(($_GET['status']=="true" || $_GET['status']=="false") && isset($_GET['coid'])) {
    
    $coID = $_GET['coid'];
   if ($_GET['status'] == "true") $sql_data_array = array('content_status' => 1);
   else $sql_data_array = array('content_status' => 0);
   os_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array, 'update', "content_id = '" . $coID . "'");
   os_redirect(os_href_link(FILENAME_CONTENT_MANAGER));
}


 if ($_GET['id']=='update' or $_GET['id']=='insert') {
        
        $group_ids='';
        if(isset($_POST['groups'])) foreach($_POST['groups'] as $b){
        $group_ids .= 'c_'.$b."_group ,";
        }
        $customers_statuses_array=os_get_customers_statuses();
        if (strpos($group_ids,'c_all_group')) {
        $group_ids='c_all_group,';
         for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
            $group_ids .='c_'.$customers_statuses_array[$i]['id'].'_group,';
         }
        }
        
        $content_title=os_db_prepare_input($_POST['cont_title']);
        $content_header=os_db_prepare_input($_POST['cont_heading']);
        $content_url=os_db_prepare_input($_POST['cont_url']);
        $content_page_url=os_db_prepare_input($_POST['cont_page_url']);
        $content_text=os_db_prepare_input($_POST['cont']);
        $coID=os_db_prepare_input($_POST['coID']);
        $upload_file=os_db_prepare_input($_POST['file_upload']);
        $content_status=os_db_prepare_input($_POST['status']);
        $content_language=os_db_prepare_input($_POST['language']);
        $select_file=os_db_prepare_input($_POST['select_file']);
        $file_flag=os_db_prepare_input($_POST['file_flag']);
        $parent_check=os_db_prepare_input($_POST['parent_check']);
        $parent_id=os_db_prepare_input($_POST['parent']);
        $group_id=os_db_prepare_input($_POST['content_group']);
        $group_ids = $group_ids;
        $sort_order=os_db_prepare_input($_POST['sort_order']);
        $content_meta_title = os_db_prepare_input($_POST['cont_meta_title']);
        $content_meta_description = os_db_prepare_input($_POST['cont_meta_description']);
        $content_meta_keywords = os_db_prepare_input($_POST['cont_meta_keywords']);
        
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		    if ($languages[$i]['status']==1)
			{
                if ($languages[$i]['code']==$content_language) $content_language=$languages[$i]['id'];
			}	
        }
        
        $error=false;
        if (strlen($content_title) < 1) {
          $error = true;
          $messageStack->add(ERROR_TITLE,'error');
        }  

        if ($content_status=='yes'){
        $content_status=1;
        } else{
        $content_status=0;
        } 

        if ($parent_check=='yes'){
        $parent_id=$parent_id;
        } else{
        $parent_id='0';
        } 

      if ($error == false) {
      if ($select_file!='default') $content_file_name=$select_file;
      
      if ($content_file = &os_try_upload('file_upload', DIR_FS_CATALOG.'media/content/')) {
        $content_file_name=$content_file->filename;
      } 
        
          $sql_data_array = array(
                                'languages_id' => $content_language,
                                'content_title' => $content_title,
                                'content_heading' => $content_header,
                                'content_page_url' => $content_page_url,
                                'content_url' => $content_url,
                                'content_text' => $content_text,
                                'content_file' => $content_file_name,
                                'content_status' => $content_status,
                                'parent_id' => $parent_id,
                                'group_ids' => $group_ids,
                                'content_group' => $group_id,
                                'sort_order' => $sort_order,
                                'file_flag' => $file_flag,
         						     'content_meta_title' => $content_meta_title,
                                'content_meta_description' => $content_meta_description,
                                'content_meta_keywords' => $content_meta_keywords);
         if ($_GET['id']=='update') {
         os_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array, 'update', "content_id = '" . $coID . "'");
        } else {
         os_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array);
        } 
        os_redirect(os_href_link(FILENAME_CONTENT_MANAGER));
        } 
        }
 
 if ($_GET['id']=='update_product' or $_GET['id']=='insert_product') {
        
        $group_ids='';
        if(isset($_POST['groups'])) foreach($_POST['groups'] as $b){
        $group_ids .= 'c_'.$b."_group ,";
        }
        $customers_statuses_array=os_get_customers_statuses();
        if (strpos($group_ids,'c_all_group')) {
        $group_ids='c_all_group,';
         for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
            $group_ids .='c_'.$customers_statuses_array[$i]['id'].'_group,';
         }
        }
        
        $content_title=os_db_prepare_input($_POST['cont_title']);
        $content_link=os_db_prepare_input($_POST['cont_link']);
        $content_language=os_db_prepare_input($_POST['language']);
        $product=os_db_prepare_input($_POST['product']);
        $upload_file=os_db_prepare_input($_POST['file_upload']);
        $filename=os_db_prepare_input($_POST['file_name']);
        $coID=os_db_prepare_input($_POST['coID']);
        $file_comment=os_db_prepare_input($_POST['file_comment']);
        $select_file=os_db_prepare_input($_POST['select_file']);
        $group_ids = $group_ids;
        
        $error=false;
        
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) 
		{
		   if ($languages[$i]['status']==1)
		   {
             if ($languages[$i]['code']==$content_language) $content_language=$languages[$i]['id'];
		   }
        }
        
        if (strlen($content_title) < 1) {
          $error = true;
          $messageStack->add(ERROR_TITLE,'error');
        } 
        
        
        if ($error == false) {
        	
// mkdir() wont work with php in safe_mode
        if  (!is_dir(DIR_FS_CATALOG.'media/products/'.$product.'/')) {
        
        $old_umask = umask(0);
	os_mkdirs(DIR_FS_CATALOG.'media/products/'.$product.'/',0777);
        umask($old_umask);

        }
if ($select_file=='default') {
        
        if ($content_file = &os_try_upload('file_upload', DIR_FS_CATALOG.'media/products/')) {
        $content_file_name=$content_file->filename;
        $old_filename=$content_file->filename;
        $timestamp=str_replace('.','',microtime());
        $timestamp=str_replace(' ','',$timestamp);
        $content_file_name=$timestamp.strstr($content_file_name,'.');
        $rename_string=DIR_FS_CATALOG.'media/products/'.$content_file_name;
        rename(DIR_FS_CATALOG.'media/products/'.$old_filename,$rename_string);
        copy($rename_string,DIR_FS_CATALOG.'media/products/backup/'.$content_file_name);
        } 
        if ($content_file_name=='') $content_file_name=$filename;
 } else {
  $content_file_name=$select_file;
}     
        $group_ids='';
        if(isset($_POST['groups'])) foreach($_POST['groups'] as $b){
        $group_ids .= 'c_'.$b."_group ,";
        }
        $customers_statuses_array=os_get_customers_statuses();
        if (strpos($group_ids,'c_all_group')) {
        $group_ids='c_all_group,';
         for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
            $group_ids .='c_'.$customers_statuses_array[$i]['id'].'_group,';
         }
        }
        
          $sql_data_array = array(
                                'products_id' => $product,
                                'group_ids' => $group_ids, 
                                'content_name' => $content_title,
                                'content_file' => $content_file_name,
                                'content_link' => $content_link,
                                'file_comment' => $file_comment,
                                'languages_id' => $content_language);
        
         if ($_GET['id']=='update_product') {
         os_db_perform(TABLE_PRODUCTS_CONTENT, $sql_data_array, 'update', "content_id = '" . $coID . "'");
         $content_id = os_db_insert_id();
        } else {
         os_db_perform(TABLE_PRODUCTS_CONTENT, $sql_data_array);
         $content_id = os_db_insert_id();        
        }  
        os_redirect(os_href_link(FILENAME_CONTENT_MANAGER,'pID='.$product));
        }

        
}
 
  add_action('head_admin', 'head_content');
  
  function head_content()
  {
     _e('<script type="text/javascript" src="includes/javascript/tabber.js"></script>');
     _e('<link rel="stylesheet" href="includes/javascript/tabber.css" TYPE="text/css" MEDIA="screen">');
     _e('<link rel="stylesheet" href="includes/javascript/tabber-print.css" TYPE="text/css" MEDIA="print">');

     $query=os_db_query("SELECT code FROM ". TABLE_LANGUAGES ." WHERE languages_id='".$_SESSION['languages_id']."'");
     $data=os_db_fetch_array($query);
     if ($_GET['action']!='new_products_content' && $_GET['action']!='') echo os_wysiwyg_tiny('content_manager',$data['code']);
     if ($_GET['action']=='new_products_content') echo os_wysiwyg_tiny('products_content',$data['code']);
     if ($_GET['action']=='edit_products_content') echo os_wysiwyg_tiny('products_content',$data['code']); 
  }
?>

<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('page_copy.png',HEADING_TITLE); ?> 
    
<?php
if (!$_GET['action']) {
?>
<div class="main"><?php echo CONTENT_NOTE; ?></div>
 <?php
 os_spaceUsed(DIR_FS_CATALOG.'media/content/');
echo '<div class="main">'.USED_SPACE.os_format_filesize($total).'</div>';
?>

<div class="tabber">
<?php

for ($i = 0, $n = sizeof($languages); $i < $n; $i++) 
{
    if ($languages[$i]['status']==1)
	{
        $content=array();


         $content_query=os_db_query("SELECT
                                        content_id,
                                        categories_id,
                                        parent_id,
                                        group_ids,
                                        languages_id,
                                        content_title,
                                        content_heading,
                                        content_url,
                                        content_text,
                                        sort_order,
                                        file_flag,
                                        content_file,
                                        content_status,
                                        content_group,
                                        content_delete,
             							       content_meta_title,
                                        content_meta_description,
                                        content_meta_keywords
                                        FROM ".TABLE_CONTENT_MANAGER."
                                        WHERE languages_id='".$languages[$i]['id']."'
                                        order by sort_order 
                                        ");
        while ($content_data=os_db_fetch_array($content_query)) {
        
         $content[]=array(
                        'CONTENT_ID' =>$content_data['content_id'] ,
                        'PARENT_ID' => $content_data['parent_id'],
                        'GROUP_IDS' => $content_data['group_ids'],
                        'LANGUAGES_ID' => $content_data['languages_id'],
                        'CONTENT_TITLE' => $content_data['content_title'],
                        'CONTENT_HEADING' => $content_data['content_heading'],
                        'CONTENT_URL' => $content_data['content_url'],
                        'CONTENT_TEXT' => $content_data['content_text'],
                        'SORT_ORDER' => $content_data['sort_order'],
                        'FILE_FLAG' => $content_data['file_flag'],
                        'CONTENT_FILE' => $content_data['content_file'],
                        'CONTENT_DELETE' => $content_data['content_delete'],
                        'CONTENT_GROUP' => $content_data['content_group'],
                        'CONTENT_STATUS' => $content_data['content_status'],
                        'CONTENT_META_TITLE' => $content_data['content_meta_title'],
                        'CONTENT_META_DESCRIPTION' => $content_data['content_meta_description'],
                        'CONTENT_META_KEYWORDS' => $content_data['content_meta_keywords']);
                                
        }
        
     
?>
        <div class="tabbertab"><h3><?php echo $languages[$i]['name']; ?></h3>
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow" align="center">
                <td class="dataTableHeadingContent" width="10" ><?php echo TABLE_HEADING_CONTENT_ID; ?></td>
                <td class="dataTableHeadingContent" width="10" >&nbsp;</td>
                <td class="dataTableHeadingContent" width="30%"><?php echo TABLE_HEADING_CONTENT_TITLE; ?></td>
                <td class="dataTableHeadingContent" width="1%"><?php echo TABLE_HEADING_CONTENT_GROUP; ?></td>
                <td class="dataTableHeadingContent" width="1%"><?php echo TABLE_HEADING_CONTENT_SORT; ?></td>
                <td class="dataTableHeadingContent" class="right_box"><?php echo TABLE_HEADING_CONTENT_FILE; ?></td>
                <td class="dataTableHeadingContent" nowrap width="5%"><?php echo TABLE_HEADING_CONTENT_STATUS; ?></td>
                <td class="dataTableHeadingContent" nowrap width=""><?php echo TABLE_HEADING_CONTENT_BOX; ?></td>
                <td class="dataTableHeadingContent" width="30%"><?php echo TABLE_HEADING_CONTENT_ACTION; ?>&nbsp;</td>
              </tr>
<?php 

for ($ii = 0, $nn = sizeof($content); $ii < $nn; $ii++)
{
   $file_flag_sql = os_db_query("SELECT file_flag_name FROM " . TABLE_CM_FILE_FLAGS . " WHERE file_flag=" . $content[$ii]['FILE_FLAG']);
   $file_flag_result = os_db_fetch_array($file_flag_sql);
   $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
   echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'">' . "\n";
   if ($content[$ii]['CONTENT_FILE']=='') $content[$ii]['CONTENT_FILE']='database';
?>
 <td class="dataTableContent" align="left"><?php echo $content[$ii]['CONTENT_ID']; ?></td>
 <td bgcolor="<?php echo substr((6543216554/$content[$ii]['CONTENT_GROUP']),0,6); ?>" class="dataTableContent" align="left">&nbsp;</td>
 <td class="dataTableContent" align="left"><?php echo $content[$ii]['CONTENT_TITLE']; ?>
 <?php
 if ($content[$ii]['CONTENT_DELETE']=='0'){
 echo '<font color="ff0000">*</font>';
} ?>
</td>
<td class="dataTableContent" align="middle"><?php echo $content[$ii]['CONTENT_GROUP']; ?></td>
<td class="dataTableContent" align="middle"><?php echo $content[$ii]['SORT_ORDER']; ?>&nbsp;</td>
<td class="dataTableContent" align="left"><?php echo $content[$ii]['CONTENT_FILE']; ?></td>
<td class="dataTableContent" align="middle">
<?php 
   if ($content[$ii]['CONTENT_STATUS']==0) 
   { 
	   echo '<a href="content_manager.php?'.'status=true&coid='.$content[$ii]['CONTENT_ID'].'">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
   } 
   else 
   { 
	   echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="content_manager.php?status=false&coid='.$content[$ii]['CONTENT_ID']. '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';	  
		 
   } 
?>
</td>
<td class="dataTableContent" align="middle"><?php echo $file_flag_result['file_flag_name']; ?></td>
<td class="dataTableContent" align="right">
<a href="">
<?php
 if ($content[$ii]['CONTENT_DELETE']=='1'){
?>
 <a href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'special=delete&coID='.$content[$ii]['CONTENT_ID']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
 <?php echo os_image(http_path('icons_admin').'delete.gif','','','','style="cursor:pointer" onClick="return confirm(\''.DELETE_ENTRY.'\')"').'  '.TEXT_DELETE.'</a>&nbsp;&nbsp;';
} // if content
?>
 <a href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'action=edit&coID='.$content[$ii]['CONTENT_ID']); ?>">
<?php echo os_image(http_path('icons_admin').'icon_edit.gif','','','','style="cursor:pointer"').'  '.TEXT_EDIT.'</a>'; ?>
 <a style="cursor:pointer" onClick="javascript:window.open('<?php echo os_href_link(FILENAME_CONTENT_PREVIEW,'coID='.$content[$ii]['CONTENT_ID']); ?>', 'popup', 'toolbar=0, width=640, height=600')"><?php echo os_image(http_path('icons_admin').'preview.gif','','','','style="cursor:pointer"').'&nbsp;&nbsp;'.TEXT_PREVIEW.'</a>'; ?>
 </td>
 </tr>
 
 <?php
 $content_1=array();
         $content_1_query=os_db_query("SELECT
                                        content_id,
                                        categories_id,
                                        parent_id,
                                        group_ids,
                                        languages_id,
                                        content_title,
                                        content_heading,
                                        content_url,
                                        content_text,
                                        file_flag,
                                        content_file,
                                        content_status,
                                        content_delete,
             							       content_meta_title,
                                        content_meta_description,
                                        content_meta_keywords
                                        FROM ".TABLE_CONTENT_MANAGER."
                                        WHERE languages_id='".$i."'
                                        AND parent_id='".$content[$ii]['CONTENT_ID']."'
                                        order by sort_order
                                         ");
        while ($content_1_data=os_db_fetch_array($content_1_query)) {
        
         $content_1[]=array(
                        'CONTENT_ID' =>$content_1_data['content_id'] ,
                        'PARENT_ID' => $content_1_data['parent_id'],
                        'GROUP_IDS' => $content_1_data['group_ids'],
                        'LANGUAGES_ID' => $content_1_data['languages_id'],
                        'CONTENT_TITLE' => $content_1_data['content_title'],
                        'CONTENT_HEADING' => $content_1_data['content_heading'],
                        'CONTENT_URL' => $content_1_data['content_url'],
                        'CONTENT_TEXT' => $content_1_data['content_text'],
                        'SORT_ORDER' => $content_1_data['sort_order'],
                        'FILE_FLAG' => $content_1_data['file_flag'],
                        'CONTENT_FILE' => $content_1_data['content_file'],
                        'CONTENT_DELETE' => $content_1_data['content_delete'],
                        'CONTENT_STATUS' => $content_data['content_status'],
                        'CONTENT_META_TITLE' => $content_data['content_meta_title'],
                        'CONTENT_META_DESCRIPTION' => $content_data['content_meta_description'],
                        'CONTENT_META_KEYWORDS' => $content_data['content_meta_keywords']);
 }      
for ($a = 0, $x = sizeof($content_1); $a < $x; $a++) {
if ($content_1[$a]!='') {
 $file_flag_sql = os_db_query("SELECT file_flag_name FROM " . TABLE_CM_FILE_FLAGS . " WHERE file_flag=" . $content_1[$a]['FILE_FLAG']);
 $file_flag_result = os_db_fetch_array($file_flag_sql);
 echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\'" onmouseout="this.className=\'dataTableRow\'">' . "\n";
 
 if ($content_1[$a]['CONTENT_FILE']=='') $content_1[$a]['CONTENT_FILE']='database';
 ?>
 <td class="dataTableContent" align="left"><?php echo $content_1[$a]['CONTENT_ID']; ?></td>
 <td class="dataTableContent" align="left">--<?php echo $content_1[$a]['CONTENT_TITLE']; ?></td>
 <td class="dataTableContent" align="left"><?php echo $content_1[$a]['CONTENT_FILE']; ?></td>
 <td class="dataTableContent" align="middle"><?php if ($content_1[$a]['CONTENT_STATUS']==0) { echo TEXT_NO; } else { echo TEXT_YES; } ?></td>
 <td class="dataTableContent" align="middle"><?php echo $file_flag_result['file_flag_name']; ?></td>
 <td class="dataTableContent" align="right">
 <a href="">
<?php
 if ($content_1[$a]['CONTENT_DELETE']=='1'){
?>
 <a href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'special=delete&coID='.$content_1[$a]['CONTENT_ID']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
 <?php echo os_image(http_path('icons_admin').'delete.gif','','','','style="cursor:pointer" onClick="return confirm(\''.DELETE_ENTRY.'\')"').'  '.TEXT_DELETE.'</a>&nbsp;&nbsp;';
} 
?>
 <a href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'action=edit&coID='.$content_1[$a]['CONTENT_ID']); ?>">
<?php echo os_image(http_path('icons_admin').'icon_edit.gif','','','','style="cursor:pointer"').'  '.TEXT_EDIT.'</a>'; ?>
 <a style="cursor:pointer" onClick="javascript:window.open('<?php echo os_href_link(FILENAME_CONTENT_PREVIEW,'coID='.$content_1[$a]['CONTENT_ID']); ?>', 'popup', 'toolbar=0, width=640, height=600')"
 
 
 ><?php echo os_image(http_path('icons_admin').'preview.gif','','','','style="cursor:pointer"').'&nbsp;&nbsp;'.TEXT_PREVIEW.'</a>'; ?>
 </td>
 </tr> 
 
 
<?php
}}
}
}
?>
</table>
</div>

<?php
}
?>
</div>
<?php
} else {

switch ($_GET['action']) {
 case 'new':    
 case 'edit':
 if ($_GET['action']!='new') {

        $content_query=os_db_query("SELECT
                                        content_id,
                                        categories_id,
                                        parent_id,
                                        group_ids,
                                        languages_id,
                                        content_title,
                                        content_heading,
                                        content_url,
                                        content_page_url,
                                        content_text,
                                        sort_order,
                                        file_flag,
                                        content_file,
                                        content_status,
                                        content_group,
                                        content_delete,
                                        content_meta_title,
                                        content_meta_description,
                                        content_meta_keywords
                                        FROM ".TABLE_CONTENT_MANAGER."
                                        WHERE content_id='".(int)$_GET['coID']."'");

        $content=os_db_fetch_array($content_query);
}
        $languages_array = array();


        
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) 
  {
      if ($languages[$i]['status']==1)
	  {                     
  if ($languages[$i]['id']==$content['languages_id']) {
         $languages_selected=$languages[$i]['code'];
         $languages_id=$languages[$i]['id'];
        }               
    $languages_array[] = array('id' => $languages[$i]['code'],
               'text' => $languages[$i]['name']);
      }
  } 
  if ($languages_id!='') $query_string='languages_id='.$languages_id.' AND';
    $categories_query=os_db_query("SELECT
                                        content_id,
                                        content_title
                                        FROM ".TABLE_CONTENT_MANAGER."
                                        WHERE ".$query_string." content_id!='".(int)$_GET['coID']."'");
  while ($categories_data=os_db_fetch_array($categories_query)) {
  
  $categories_array[]=array(
                        'id'=>$categories_data['content_id'],
                        'text'=>$categories_data['content_title']);
 }   
?>
<br /><br />
<?php
 
if ($_GET['action']!='new') 
{
   echo os_draw_form('edit_content',FILENAME_CONTENT_MANAGER,'action=edit&id=update&coID='.$_GET['coID'],'post','enctype="multipart/form-data"').os_draw_hidden_field('coID',$_GET['coID']);
} 
else 
{
   echo os_draw_form('edit_content',FILENAME_CONTENT_MANAGER,'action=edit&id=insert','post','enctype="multipart/form-data"').   os_draw_hidden_field('coID',$_GET['coID']);
} ?>
<table class="main" width="100%" border="0">
   <tr> 
      <td width="10%"><?php echo TEXT_LANGUAGE; ?></td>
      <td width="90%"><?php echo os_draw_pull_down_menu('language',$languages_array,$languages_selected); ?></td>
   </tr>
<?php
if ($content['content_delete']!=0 or $_GET['action']=='new') {
?>   
      <tr> 
      <td width="10%"><?php echo TEXT_GROUP; ?></td>
      <td width="90%"><?php echo os_draw_input_field('content_group',$content['content_group'],'size="5"'); ?><?php echo TEXT_GROUP_DESC; ?></td>
   </tr>
<?php
} else {
echo os_draw_hidden_field('content_group',$content['content_group']);
?>
      <tr>
      <td width="10%"><?php echo TEXT_GROUP; ?></td>
      <td width="90%"><?php echo $content['content_group']; ?></td>
   </tr>
<?php
}
$file_flag_sql = os_db_query("SELECT file_flag as id, file_flag_name as text FROM " . TABLE_CM_FILE_FLAGS);
while($file_flag = os_db_fetch_array($file_flag_sql)) {
	$file_flag_array[] = array('id' => $file_flag['id'], 'text' => $file_flag['text']);
}
?>	
      <tr> 
      <td width="10%"><?php echo TEXT_FILE_FLAG; ?></td>
      <td width="90%"><?php echo os_draw_pull_down_menu('file_flag',$file_flag_array,$content['file_flag']); ?></td>
   </tr>

      <tr>
      <td width="10%"><?php echo TEXT_PARENT; ?></td>
      <td width="90%"><?php echo os_draw_pull_down_menu('parent',$categories_array,$content['parent_id']); ?><?php echo os_draw_checkbox_field('parent_check', 'yes',false).' '.TEXT_PARENT_DESCRIPTION; ?></td>
   </tr>

    <tr>
      <td width="10%"><?php echo TEXT_SORT_ORDER; ?></td>
      <td width="90%"><?php echo os_draw_input_field('sort_order',$content['sort_order'],'size="5"'); ?></td>
    </tr>

      <tr> 
      <td valign="top" width="10%"><?php echo TEXT_STATUS; ?></td>
      <td width="90%"><?php
      if ($content['content_status']=='1') {
      echo os_draw_checkbox_field('status', 'yes',true).' '.TEXT_STATUS_DESCRIPTION;
      } else {
      echo os_draw_checkbox_field('status', 'yes',false).' '.TEXT_STATUS_DESCRIPTION;
      }

      ?><br /><br /></td>
   </tr>

          <?php
if (GROUP_CHECK=='true') {
$customers_statuses_array = os_get_customers_statuses();
$customers_statuses_array=array_merge(array(array('id'=>'all','text'=>TXT_ALL)),$customers_statuses_array);
?>
<tr>
<td style="border-top: 1px solid; border-color: #ff0000;" valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
<td style="border-top: 1px solid; border-left: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-right: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-bottom: 1px solid; border-color: #ff0000;" bgcolor="#FFCC33" class="main">
<?php

for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) 
{
   if (strstr($content['group_ids'],'c_'.$customers_statuses_array[$i]['id'].'_group')) 
   {
      $checked='checked ';
   } 
   else 
   {
      $checked=''; 
   }
   $check_all = '';
   if ($customers_statuses_array[$i]['id'] == 'all') $check_all = 'onClick="javascript:CheckAllContent(this.checked);"';
   echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.          $customers_statuses_array[$i]['text'].'<br />';
}
?>
</td>
</tr>
<?php
}
?>


   <tr>
      <td width="10%"><?php echo TEXT_TITLE; ?></td>
      <td width="90%"><?php echo os_draw_input_field('cont_title',$content['content_title'],'size="60"'); ?></td>
   </tr>
   <tr>
      <td width="10%"><?php echo TEXT_PAGE_URL; ?></td>
      <td width="90%"><?php echo os_draw_input_field('cont_page_url',$content['content_page_url'],'size="60"'); ?></td>
   </tr>
   <tr> 
      <td width="10%"><?php echo TEXT_HEADING; ?></td>
      <td width="90%"><?php echo os_draw_input_field('cont_heading',$content['content_heading'],'size="60"'); ?></td>
   </tr>

   <tr>
   	   <td width="10%"><?php echo TEXT_META_TITLE; ?></td>
      <td width="90%"><?php echo os_draw_input_field('cont_meta_title',$content['content_meta_title'],'size="60"'); ?></td>
   </tr>

   <tr> 
      <td width="10%"><?php echo TEXT_META_DESCRIPTION; ?></td>
      <td width="90%"><?php echo os_draw_input_field('cont_meta_description',$content['content_meta_description'],'size="60"'); ?></td>
   </tr>

   <tr> 
      <td width="10%"><?php echo TEXT_META_KEYWORDS; ?></td>
      <td width="90%"><?php echo os_draw_input_field('cont_meta_keywords',$content['content_meta_keywords'],'size="60"'); ?></td>
   </tr>

   <tr> 
      <td width="10%" valign="top"><?php echo TEXT_UPLOAD_FILE; ?></td>
      <td width="90%"><?php echo os_draw_file_field('file_upload').' '.TEXT_UPLOAD_FILE_LOCAL; ?></td>
   </tr> 
         <tr> 
      <td width="10%" valign="top"><?php echo TEXT_CHOOSE_FILE; ?></td>
      <td width="90%">
<?php
    require_once(dir_path('func_admin').'file_system.php');
    $files = os_get_filelist(DIR_FS_CATALOG.'media/content/','', array('index.html'));

if ($content['content_file']=='') {
    $default_array[]=array('id' => 'default','text' => TEXT_SELECT);
    $default_value='default';
    if (count($files) == 0)
    {
    $files = $default_array;
    }
    else
    {
    $files=os_array_merge($default_array,$files);
    }
} else {
$default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
$default_value=$content['content_file'];
    if (count($files) == 0)
    {
    $files = $default_array;
    }
    else
    {
    $files=array_merge($default_array,$files);
    }
}
echo '<br />'.TEXT_CHOOSE_FILE_SERVER.'</br>';
echo os_draw_pull_down_menu('select_file',$files,$default_value);
      if ($content['content_file']!='') {
        echo TEXT_CURRENT_FILE.' <b>'.$content['content_file'].'</b><br />';
        }



?>
      </td>
      </td>
   </tr> 
   <tr> 
      <td width="10%" valign="top"></td>
      <td colspan="90%" valign="top"><br /><?php echo TEXT_FILE_DESCRIPTION; ?></td>
   </tr> 
   <tr> 
      <td width="10%" valign="top"><?php echo TEXT_CONTENT; ?></td>
      
      <td width="90%">
   <?php
echo os_draw_textarea_field('cont','','100%','35',$content['content_text']);
?><br /><a href="javascript:toggleHTMLEditor('cont');" class="code"><?php echo TEXT_EDIT_E;?></a>
      </td>
   </tr>
  
     <tr> 
      <td width="10%"><?php echo TEXT_URL; ?></td>
      <td width="90%"><?php echo os_draw_input_field('cont_url',$content['content_url'],'size="60"'); ?></td>
   </tr>
 
    <tr>
        <td colspan="2" align="right" class="main"><?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>'; ?><a class="button" onClick="this.blur();" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER); ?>"><span><?php echo BUTTON_BACK; ?></span></a></td>
   </tr>
</table>
</form>
<?php
 break;
 
 case 'edit_products_content':
 case 'new_products_content':
 
  if ($_GET['action']=='edit_products_content') {
        $content_query=os_db_query("SELECT
                                        content_id,
                                        products_id,
                                        group_ids,
                                        content_name,
                                        content_file,
                                        content_link,
                                        languages_id,
                                        file_comment,
                                        content_read

                                        FROM ".TABLE_PRODUCTS_CONTENT."
                                        WHERE content_id='".(int)$_GET['coID']."'");

        $content=os_db_fetch_array($content_query);
}
 
 $products_query=os_db_query("SELECT
                                products_id,
                                products_name
                                FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                WHERE language_id='".(int)$_SESSION['languages_id']."'");
 $products_array=array();

 while ($products_data=os_db_fetch_array($products_query)) {
 
 $products_array[]=array(
                        'id' => $products_data['products_id'],  
                        'text' => $products_data['products_name']);
}

 $languages_array = array();


        
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) 
  {
     if ($languages[$i]['status']==1)
     {	 
        if ($languages[$i]['id']==$content['languages_id']) 
		{
          $languages_selected=$languages[$i]['code'];
          $languages_id=$languages[$i]['id'];
        }               
    $languages_array[] = array('id' => $languages[$i]['code'],
               'text' => $languages[$i]['name']);
     }
  } 
  $content_files_query=os_db_query("SELECT DISTINCT
                                content_name,
                                content_file
                                FROM ".TABLE_PRODUCTS_CONTENT."
                                WHERE content_file!=''");
 $content_files=array();

 while ($content_files_data=os_db_fetch_array($content_files_query)) {

     $content_files[]=array(
     'id' => $content_files_data['content_file'],
     'text' => $content_files_data['content_name']);
 }

 $default_array[]=array('id' => 'default','text' => TEXT_SELECT);
 $default_value='default';
 $content_files=array_merge($default_array,$content_files);
 
if ($_GET['action']!='new_products_content') 
{
 ?>     
 <?php echo os_draw_form('edit_content',FILENAME_CONTENT_MANAGER,'action=edit_products_content&id=update_product&coID='.$_GET['coID'],'post','enctype="multipart/form-data"').os_draw_hidden_field('coID',$_GET['coID']); ?>
<?php
} 
else
{
?>
<?php echo os_draw_form('edit_content',FILENAME_CONTENT_MANAGER,'action=edit_products_content&id=insert_product','post','enctype="multipart/form-data"');   ?>
<?php
}
?>
 <div class="main"><?php echo TEXT_CONTENT_DESCRIPTION; ?></div>
 <table class="main" width="100%" border="0">
   <tr>
      <td width="10%"><?php echo TEXT_PRODUCT; ?></td>
      <td width="90%"><?php echo os_draw_pull_down_menu('product',$products_array,$content['products_id']); ?></td>
   </tr>
      <tr> 
      <td width="10%"><?php echo TEXT_LANGUAGE; ?></td>
      <td width="90%"><?php echo os_draw_pull_down_menu('language',$languages_array,$languages_selected); ?></td>
   </tr>

          <?php
if (GROUP_CHECK=='true') {
$customers_statuses_array = os_get_customers_statuses();
$customers_statuses_array=array_merge(array(array('id'=>'all','text'=>TXT_ALL)),$customers_statuses_array);
?>
<tr>
<td style="border-top: 1px solid; border-color: #ff0000;" valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
<td style="border-top: 1px solid; border-left: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-right: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-bottom: 1px solid; border-color: #ff0000;" bgcolor="#FFCC33" class="main">
<?php

for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
if (strstr($content['group_ids'],'c_'.$customers_statuses_array[$i]['id'].'_group')) {

$checked='checked ';
} else {
$checked='';
}
echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.$customers_statuses_array[$i]['text'].'<br />';
}
?>
</td>
</tr>
<?php
}
?>

      <tr>
      <td width="10%"><?php echo TEXT_TITLE_FILE; ?></td>
      <td width="90%"><?php echo os_draw_input_field('cont_title',$content['content_name'],'size="60"'); ?></td>
   </tr>
      <tr> 
      <td width="10%"><?php echo TEXT_LINK; ?></td>
      <td width="90%"><?php  echo os_draw_input_field('cont_link',$content['content_link'],'size="60"'); ?></td>
   </tr>

      <tr>
      <td width="10%" valign="top"><?php echo TEXT_FILE_DESC; ?></td>
      <td width="90%"><?php
          echo os_draw_textarea_field('file_comment','','100','30',$content['file_comment']);
?><br /><a href="javascript:toggleHTMLEditor('file_comment');" class="code"><?php echo TEXT_EDIT_E;?></a></td>
   </tr>
         <tr> 
      <td width="10%" valign="top"><?php echo TEXT_CHOOSE_FILE; ?></td>
      <td width="90%">
<?php
    require_once(dir_path('func_admin').'file_system.php');
    $files = os_get_filelist(DIR_FS_CATALOG.'media/products/','', array('index.html'));
    unset ($default_array);
    if ($content['content_file']=='') {
         $default_array[]=array('id' => 'default','text' => TEXT_SELECT);
         $default_value='default';
    } else {
         $default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
         $default_value=$content['content_file'];
    }
    $files=os_array_merge($default_array, $files);
 
    echo '<br />'.TEXT_CHOOSE_FILE_SERVER_PRODUCTS.'</br>';
    echo os_draw_pull_down_menu('select_file',$files,$default_value);
    if ($content['content_file']!='') {
       echo TEXT_CURRENT_FILE.' <b>'.$content['content_file'].'</b><br />';
    }

?>
      </td>
      </td>
   </tr> 
      <tr> 
      <td width="10%" valign="top"><?php echo TEXT_UPLOAD_FILE; ?></td>
      <td width="90%"><?php echo os_draw_file_field('file_upload').' '.TEXT_UPLOAD_FILE_LOCAL; ?></td>
   </tr> 
 <?php
 if ($content['content_file']!='') {
 ?>
    <tr> 
      <td width="10%"><?php echo TEXT_FILENAME; ?></td>
      <td width="90%" valign="top"><?php echo os_draw_hidden_field('file_name',$content['content_file']).os_image(DIR_WS_CATALOG.'admin/images/icons/icon_'.str_replace('.','',strstr($content['content_file'],'.')).'.gif').$content['content_file']; ?></td>
    </tr>
  <?php
}
?>
       <tr>
        <td colspan="2" align="right" class="main"><?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>'; ?><a class="button" onClick="this.blur();" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER); ?>"><span><?php echo BUTTON_BACK; ?></span></a></td>
   </tr>
   </form>
   </table>
 
 <?php
 
 break;
 

}
}

if (!$_GET['action']) {
?>
<a class="button" onClick="this.blur();" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'action=new'); ?>"><span><?php echo BUTTON_NEW_CONTENT; ?></span></a>
<?php
}
?>
</td>
          </tr>                 
        </table>
 <?php
 if (!$_GET['action']) {
 
 $products_id_query=os_db_query("SELECT DISTINCT
                                pc.products_id,
                                pd.products_name
                                FROM ".TABLE_PRODUCTS_CONTENT." pc, ".TABLE_PRODUCTS_DESCRIPTION." pd
                                WHERE pd.products_id=pc.products_id and pd.language_id='".(int)$_SESSION['languages_id']."'");
 
 $products_ids=array();
 while ($products_id_data=os_db_fetch_array($products_id_query)) {
        
        $products_ids[]=array(
                        'id'=>$products_id_data['products_id'],
                        'name'=>$products_id_data['products_name']);
        
        }
        
        
 ?>
 <div class="pageHeading"><br /><?php echo HEADING_PRODUCTS_CONTENT; ?><br /></div>
  <?php
 os_spaceUsed(DIR_FS_CATALOG.'media/products/');
echo '<div class="main">'.USED_SPACE.os_format_filesize($total).'</div></br>';
?>      
 <table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr class="dataTableHeadingRow">
     <td class="dataTableHeadingContent" nowrap width="5%" ><?php echo TABLE_HEADING_PRODUCTS_ID; ?></td>
     <td class="dataTableHeadingContent" width="95%" align="left"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
</tr>
<?php

for ($i=0,$n=sizeof($products_ids); $i<$n; $i++) 
{
  $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
 echo '<tr style="background-color:'.$color.'" class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" >' . "\n";
 
 ?>
 <td align="left"><?php echo $products_ids[$i]['id']; ?></td>
 <td align="left"><b><?php echo os_image(DIR_WS_CATALOG.'images/icons/arrow.gif'); ?><a href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'pID='.$products_ids[$i]['id']);?>"><?php echo $products_ids[$i]['name']; ?></a></b></td>
 </tr>
<?php
if ($_GET['pID']) {
        $content_query=os_db_query("SELECT
                                        content_id,
                                        content_name,
                                        content_file,
                                        content_link,
                                        languages_id,
                                        file_comment,
                                        content_read
                                        FROM ".TABLE_PRODUCTS_CONTENT."
                                        WHERE products_id='".$_GET['pID']."' order by content_name");
        $content_array='';
        while ($content_data=os_db_fetch_array($content_query)) {
                
                $content_array[]=array(
                                        'id'=> $content_data['content_id'],
                                        'name'=> $content_data['content_name'],
                                        'file'=> $content_data['content_file'],
                                        'link'=> $content_data['content_link'],
                                        'comment'=> $content_data['file_comment'],
                                        'languages_id'=> $content_data['languages_id'],
                                        'read'=> $content_data['content_read']);
                                        
                } // while content data

if ($_GET['pID']==$products_ids[$i]['id']){
?>

<tr>
 <td class="dataTableContent" align="left"></td>
 <td class="dataTableContent" align="left">

 <table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" nowrap width="2%" ><?php echo TABLE_HEADING_PRODUCTS_CONTENT_ID; ?></td>
    <td class="dataTableHeadingContent" nowrap width="2%" >&nbsp;</td>
    <td class="dataTableHeadingContent" nowrap width="5%" ><?php echo TABLE_HEADING_LANGUAGE; ?></td>
    <td class="dataTableHeadingContent" nowrap width="15%" ><?php echo TABLE_HEADING_CONTENT_NAME; ?></td>
    <td class="dataTableHeadingContent" nowrap width="30%" ><?php echo TABLE_HEADING_CONTENT_FILE; ?></td>
    <td class="dataTableHeadingContent" nowrap width="1%" ><?php echo TABLE_HEADING_CONTENT_FILESIZE; ?></td>
    <td class="dataTableHeadingContent" nowrap align="middle" width="20%" ><?php echo TABLE_HEADING_CONTENT_LINK; ?></td>
    <td class="dataTableHeadingContent" nowrap width="5%" ><?php echo TABLE_HEADING_CONTENT_HITS; ?></td>
    <td class="dataTableHeadingContent" nowrap width="20%" ><?php echo TABLE_HEADING_CONTENT_ACTION; ?></td>
    </tr>  

<?php
 
 for ($ii=0,$nn=sizeof($content_array); $ii<$nn; $ii++) {

 echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\'" onmouseout="this.className=\'dataTableRow\'">' . "\n";
 
 ?>
 <td class="dataTableContent" align="left"><?php echo  $content_array[$ii]['id']; ?> </td>
 <td class="dataTableContent" align="left"><?php
 
 
 
 if ($content_array[$ii]['file']!='') {
 
 echo os_image(http_path('catalog').'admin/images/icons/icon_'.str_replace('.','',strstr($content_array[$ii]['file'],'.')).'.gif');
} else {
echo os_image(http_path('catalog').'admin/images/icons/icon_link.gif');
}

for ($xx=0,$zz=sizeof($languages); $xx<$zz;$xx++)
{
	if ($languages[$xx]['id']==$content_array[$ii]['languages_id']) {
	$lang_dir=$languages[$xx]['directory'];	
	break;
}	
}

?>
</td>
 <td class="dataTableContent" align="left"><?php echo os_image(http_path_admin('icons').'lang/'.$lang_dir.'.gif'); ?></td>
 <td class="dataTableContent" align="left"><?php echo $content_array[$ii]['name']; ?></td>
 <td class="dataTableContent" align="left"><?php echo $content_array[$ii]['file']; ?></td>
 <td class="dataTableContent" align="left"><?php echo os_filesize($content_array[$ii]['file']); ?></td>
 <td class="dataTableContent" align="left" align="middle"><?php
 if ($content_array[$ii]['link']!='') {
 echo '<a href="'.$content_array[$ii]['link'].'" target="new">'.$content_array[$ii]['link'].'</a>';
} 
 ?>
  &nbsp;</td>
 <td class="dataTableContent" align="left"><?php echo $content_array[$ii]['read']; ?></td>
 <td class="dataTableContent" align="left">
 
  <a href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'special=delete_product&coID='.$content_array[$ii]['id']).'&pID='.$products_ids[$i]['id']; ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
 <?php
 
 echo os_image(http_path('icons_admin').'delete.gif','','','','style="cursor:pointer" onClick="return confirm(\''.DELETE_ENTRY.'\')"').'  '.TEXT_DELETE.'</a>&nbsp;&nbsp;';

?>
 <a href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'action=edit_products_content&coID='.$content_array[$ii]['id']); ?>">
<?php echo os_image(http_path('icons_admin').'icon_edit.gif','','','','style="cursor:pointer"').'  '.TEXT_EDIT.'</a>'; ?>

<?php
if (	preg_match('/.gif/i',$content_array[$ii]['file'])
	or
	preg_match('/.jpg/i',$content_array[$ii]['file'])
	or
	preg_match('/.png/i',$content_array[$ii]['file'])
	or
	preg_match('/.html/i',$content_array[$ii]['file'])
	or
	preg_match('/.htm/i',$content_array[$ii]['file'])
	or
	preg_match('/.txti/',$content_array[$ii]['file'])
	or
	preg_match('/.bmp/i',$content_array[$ii]['file'])
	) {
?>
 <a style="cursor:pointer" onClick="javascript:window.open('<?php echo os_href_link(FILENAME_CONTENT_PREVIEW,'pID=media&coID='.$content_array[$ii]['id']); ?>', 'popup', 'toolbar=0, width=640, height=600')"
 
 
 ><?php echo os_image(http_path('icons_admin').'preview.gif','','','',' style="cursor:pointer"').'&nbsp;&nbsp;'.TEXT_PREVIEW.'</a>'; ?> 
<?php
}
?> 
 
 
 
 </td>
 </tr>

<?php 

} // for content_array
echo '</table></td></tr>';
}
} // for
}
?> 

       
 </table>
 <a class="button" onClick="this.blur();" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'action=new_products_content'); ?>"><span><?php echo BUTTON_NEW_CONTENT; ?></span></a>                 
 <?php
}
?>       
        
        </td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>