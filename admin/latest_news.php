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
  if (@$_GET['action']) {
    switch ($_GET['action']) 
	{
      case 'setflag':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if ($_GET['latest_news_id']) {
            os_db_query("update " . TABLE_LATEST_NEWS . " set status = '" . $_GET['flag'] . "' where news_id = '" . $_GET['latest_news_id'] . "'");
          }
        }
		set_news_url_cache();
        break;

      case 'delete_latest_news_confirm':
        if ($_POST['latest_news_id']) {
          $latest_news_id = os_db_prepare_input($_POST['latest_news_id']);
          os_db_query("delete from " . TABLE_LATEST_NEWS . " where news_id = '" . os_db_input($latest_news_id) . "'");
        }
         set_news_url_cache();
        break;

      case 'insert_latest_news': 
        if ($_POST['headline']) {
          $sql_data_array = array('headline'   => os_db_prepare_input($_POST['headline']),
		                          'news_page_url'    => os_db_prepare_input($_POST['news_page_url']),
                                  'content'    => os_db_prepare_input($_POST['content']),
                                  'date_added' => 'now()',
                                  'language'   => os_db_prepare_input($_POST['item_language']),
                                  'status'     => '1' );
          os_db_perform(TABLE_LATEST_NEWS, $sql_data_array);
          $news_id = os_db_insert_id();
		  set_news_url_cache();
        }
        break;

      case 'update_latest_news': //user wants to modify a news article.
        if($_GET['latest_news_id']) {
          $sql_data_array = array('headline' => os_db_prepare_input($_POST['headline']),
                                  'news_page_url'    => os_db_prepare_input($_POST['news_page_url']),
                                  'content'  => os_db_prepare_input($_POST['content']),
                                  'date_added'  => os_db_prepare_input($_POST['date_added']),
                                  'language'   => os_db_prepare_input($_POST['item_language']),
                                  );
          os_db_perform(TABLE_LATEST_NEWS, $sql_data_array, 'update', "news_id = '" . os_db_prepare_input($_GET['latest_news_id']) . "'");
        }
		set_news_url_cache();
  //      os_redirect(os_href_link(FILENAME_LATEST_NEWS));
        break;
    }
  }
  
  add_action('head_admin', 'head_news');
  
  function head_news ()
  {
     $query=os_db_query("SELECT code FROM ". TABLE_LANGUAGES ." WHERE languages_id='".$_SESSION['languages_id']."'");
     $data=os_db_fetch_array($query);
     if (@$_GET['action']=='new_latest_news') echo os_wysiwyg_tiny('latest_news',$data['code']); 
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (@$_GET['action'] == 'new_latest_news') 
  { //insert or edit a news item
     set_news_url_cache();
    if ( isset($_GET['latest_news_id']) ) { //editing exsiting news item
      $latest_news_query = os_db_query("select news_id, headline, news_page_url, language, date_added, content from " . TABLE_LATEST_NEWS . " where news_id = '" . $_GET['latest_news_id'] . "'");
      $latest_news = os_db_fetch_array($latest_news_query);
    } else { //adding new news item
      $latest_news = array();
    }
?>
      <tr><?php echo os_draw_form('new_latest_news', FILENAME_LATEST_NEWS, isset($_GET['latest_news_id']) ? 'latest_news_id=' . $_GET['latest_news_id'] . '&action=update_latest_news' : 'action=insert_latest_news', 'post', 'enctype="multipart/form-data"'); ?>
        <td><table border="0" cellspacing="0" cellpadding="2" width="100%">
          <tr>
            <td class="main"><?php echo TEXT_LATEST_NEWS_HEADLINE; ?>:</td>
            <td class="main"><?php echo '&nbsp;' . os_draw_input_field('headline', @$latest_news['headline'], 'size="60"', true); ?></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_NEWS_PAGE_URL; ?>:</td>
            <td class="main"><?php echo '&nbsp;' . os_draw_input_field('news_page_url', @$latest_news['news_page_url'], 'size="60"', true); ?></td>
          </tr>
		     <tr>
            <td class="main">&nbsp;</td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_LATEST_NEWS_CONTENT; ?>:</td>
            <td class="main"><?php echo '&nbsp;' . os_draw_textarea_field('content', '', '100%', '25', stripslashes(@$latest_news['content'])); ?><br /><a href="javascript:toggleHTMLEditor('content');" class="code"><?php echo TEXT_EDIT_E;?></a></td>
          </tr>

<?php
if ( isset($_GET['latest_news_id']) ) {
?>
		   <tr>
            <td class="main">&nbsp;</td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_LATEST_NEWS_DATE; ?>:</td>
            <td class="main"><?php echo os_draw_input_field('date_added', $latest_news['date_added'], '', true); ?></td>
          </tr>
		   <tr>
            <td class="main">&nbsp;</td>
          </tr>

<?php
}
?>

          <tr>
            <td class="main"><?php echo TEXT_LATEST_NEWS_LANGUAGE; ?>:</td>
            <td class="main"><?php echo '&nbsp;'; ?>

<?php

  $languages = os_get_languages();
  $languages_array = array();

  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                        
  if ($languages[$i]['id']==@$latest_news['language']) {
         $languages_selected=$languages[$i]['id'];
         $languages_id=$languages[$i]['id'];
        }               
    $languages_array[] = array('id' => $languages[$i]['id'],
               'text' => $languages[$i]['name']);

  } 
  
echo os_draw_pull_down_menu('item_language',@$languages_array,@$languages_selected); ?>

</td>
          </tr>


        </table></td>
      </tr>
      <tr>
        <td class="main" align="right">
          <?php
            isset($_GET['latest_news_id']) ? $cancel_button = '&nbsp;&nbsp;<a class="button" href="' . os_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $_GET['latest_news_id']) . '"><span>' . BUTTON_CANCEL . '</span></a>' : $cancel_button = '';
            echo '<span class="button"><button type="submit" value="' . BUTTON_INSERT .'">' . BUTTON_INSERT .'</button></span>' . $cancel_button;
          ?>
        </td>
      </form></tr>
<?php

  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LATEST_NEWS_HEADLINE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LATEST_NEWS_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_LATEST_NEWS_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $rows = 0;

    $latest_news_count = 0;
   $latest_news_query = os_db_query('select news_id, headline, news_page_url, content, status from ' . TABLE_LATEST_NEWS . ' order by date_added desc');
    
	$color = '';
	
    while ($latest_news = os_db_fetch_array($latest_news_query)) {
      $latest_news_count++;
      $rows++;
      
      if ( ((@!$_GET['latest_news_id']) || (@$_GET['latest_news_id'] == @$latest_news['news_id'])) && (@!$selected_item) && (substr(@$_GET['action'], 0, 4) != 'new_') ) {
        $selected_item = $latest_news;
      }
	  $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
      if ( (@is_array($selected_item)) && (@$latest_news['news_id'] == @$selected_item['news_id']) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $latest_news['news_id']) . '\'">' . "\n";
      } else {
        echo '              <tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $latest_news['news_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '&nbsp;' . $latest_news['headline']; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($latest_news['status'] == '1') {
        echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_LATEST_NEWS, 'action=setflag&flag=0&latest_news_id=' . $latest_news['news_id']) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . os_href_link(FILENAME_LATEST_NEWS, 'action=setflag&flag=1&latest_news_id=' . $latest_news['news_id']) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td class="dataTableContent" align="right"><?php if (@$latest_news['news_id'] == @$_GET['latest_news_id']) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $latest_news['news_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }

?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo '<br>' . TEXT_NEWS_ITEMS . '&nbsp;' . $latest_news_count; ?></td>
                    <td align="right" class="smallText"><?php echo '&nbsp;<a class="button" href="' . os_href_link(FILENAME_LATEST_NEWS, 'action=new_latest_news') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?>&nbsp;</td>
                  </tr>																																		  
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();
    switch (@$_GET['action']) {
      case 'delete_latest_news': 
        $heading[] = array('text'   => '<b>' . TEXT_INFO_HEADING_DELETE_ITEM . '</b>');
        
        $contents = array('form'    => os_draw_form('news', FILENAME_LATEST_NEWS, 'action=delete_latest_news_confirm') . os_draw_hidden_field('latest_news_id', $_GET['latest_news_id']));
        $contents[] = array('text'  => TEXT_DELETE_ITEM_INTRO);
        $contents[] = array('text'  => '<br><b>' . $selected_item['headline'] . '</b>');
        
        $contents[] = array('align' => 'center',
                            'text'  => '<br><span class="button"><button type="submit" value="' . BUTTON_DELETE .'">' . BUTTON_DELETE .'</button></span><a class="button" href="' . os_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $selected_item['news_id']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
        break;

      default:
        if ($rows > 0) {
          if (is_array($selected_item)) {
            $heading[] = array('text' => '<b>' . $selected_item['headline'] . '</b>');

            $contents[] = array('align' => 'center', 
                                'text' => '<a class="button" href="' . os_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $selected_item['news_id'] . '&action=new_latest_news') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" href="' . os_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $selected_item['news_id'] . '&action=delete_latest_news') . '"><span>' . BUTTON_DELETE . '</span></a>');

            $contents[] = array('text' => '<br>' . $selected_item['content']);
          }
        } else {
          $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');

          $contents[] = array('text' => sprintf(TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS, $parent_categories_name));
        }
        break;
    }

    if ( (os_not_null($heading)) && (os_not_null($contents)) ) {
      echo '            <td class="right_box" valign="top">' . "\n";

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }
?>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>