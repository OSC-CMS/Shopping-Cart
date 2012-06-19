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

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (os_not_null($action)) {
    switch (@$action) {
      case 'insert':
      case 'save':
        if (isset($_GET['auID'])) $authors_id = os_db_prepare_input($_GET['auID']);
        $authors_name = os_db_prepare_input($_POST['authors_name']);

        $sql_data_array = array('authors_name' => $authors_name);

        if ($action == 'insert') {
          $insert_sql_data = array('date_added' => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          os_db_perform(TABLE_AUTHORS, $sql_data_array);
          $authors_id = os_db_insert_id();
        } elseif ($action == 'save') {
          $update_sql_data = array('last_modified' => 'now()');

          $sql_data_array = array_merge($sql_data_array, $update_sql_data);

          os_db_perform(TABLE_AUTHORS, $sql_data_array, 'update', "authors_id = '" . (int)$authors_id . "'");
        }

        $languages = os_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $authors_desc_array = $_POST['authors_description'];
          $authors_url_array = $_POST['authors_url'];
          $language_id = $languages[$i]['id'];

          $sql_data_array = array('authors_description' => os_db_prepare_input($authors_desc_array[$language_id]),
                                  'authors_url' => os_db_prepare_input($authors_url_array[$language_id]));

          if ($action == 'insert') {
            $insert_sql_data = array('authors_id' => $authors_id,
                                     'languages_id' => $language_id);

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            os_db_perform(TABLE_AUTHORS_INFO, $sql_data_array);
          } elseif ($action == 'save') {
            os_db_perform(TABLE_AUTHORS_INFO, $sql_data_array, 'update', "authors_id = '" . (int)$authors_id . "' and languages_id = '" . (int)$language_id . "'");
          }
        }

        if (USE_CACHE == 'true') {
          os_reset_cache_block('authors');
        }

        os_redirect(os_href_link(FILENAME_AUTHORS, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'auID=' . $authors_id));
        break;
      case 'deleteconfirm':
        $authors_id = os_db_prepare_input($_GET['auID']);

        os_db_query("delete from " . TABLE_AUTHORS . " where authors_id = '" . (int)$authors_id . "'");
        os_db_query("delete from " . TABLE_AUTHORS_INFO . " where authors_id = '" . (int)$authors_id . "'");

        if (isset($_POST['delete_articles']) && ($_POST['delete_articles'] == 'on')) {
          $articles_query = os_db_query("select articles_id from " . TABLE_ARTICLES . " where authors_id = '" . (int)$authors_id . "'");
          while ($articles = os_db_fetch_array($articles_query)) {
            os_remove_article($articles['articles_id']);
          }
        } else {
          os_db_query("update " . TABLE_ARTICLES . " set authors_id = '' where authors_id = '" . (int)$authors_id . "'");
        }

        if (USE_CACHE == 'true') {
          os_reset_cache_block('authors');
        }

        os_redirect(os_href_link(FILENAME_AUTHORS, 'page=' . $_GET['page']));
        break;
    }
  }
  
  add_action('head_admin', 'head_authors');
  
  function head_authors()
  {
    _e('<script language="javascript"><!--
function popupImageWindow(url) {
  window.open(url,\'popupImageWindow\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150\')
}
//--></script>');
  }
?>

<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ($action == 'new') {
?>
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" class="pageHeading">	
			<?php $main->heading('bug.png', TEXT_HEADING_NEW_AUTHOR); ?> 
			</td>
          </tr>
        </table></td>
      </tr>
      <tr><?php echo os_draw_form('authors', FILENAME_AUTHORS, 'action=insert', 'post', 'enctype="multipart/form-data"'); ?>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" colspan="2"><?php echo TEXT_NEW_INTRO; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_AUTHORS_NAME; ?></td>
            <td class="main"><?php echo '&nbsp;' . os_draw_input_field('authors_name', '', 'size="20"'); ?></td>
          </tr>
      <tr>
        <td class="main">&nbsp;</td>
        <td class="main" align="left"><?php echo '&nbsp;' . '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>' . ' <a class="button" href="' . os_href_link(FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $_GET['auID']) . '"><span>' . BUTTON_CANCEL . '</span></a>'; ?></td>
      </form>
      </tr>
          </tr>
        </table></td>
      </tr>
<?php
  } elseif ($action == 'edit') {

    $authors_query = os_db_query("select authors_id, authors_name from " . TABLE_AUTHORS . " where authors_id = '" . $_GET['auID'] . "'");
    $authors = os_db_fetch_array($authors_query)
?>
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" class="pageHeading"><h1 class="contentBoxHeading"><?php echo TEXT_HEADING_EDIT_AUTHOR; ?></h1></td>
          </tr>
        </table></td>
      </tr>
      <tr><?php echo os_draw_form('authors', FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $authors['authors_id'] . '&action=save', 'post', 'enctype="multipart/form-data"'); ?>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" colspan="2"><?php echo TEXT_EDIT_INTRO; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_AUTHORS_NAME; ?></td>
            <td class="main"><?php echo '&nbsp;' . os_draw_input_field('authors_name', $authors['authors_name'], 'size="20"'); ?></td>
          </tr>
      <tr>
        <td class="main">&nbsp;</td>
        <td class="main" align="left"><?php echo  '&nbsp;' . '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>' . ' <a class="button" href="' . os_href_link(FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $authors['authors_id']) . '"><span>' . BUTTON_CANCEL . '</span></a>'; ?></td>
      </form>
      </tr>
          </tr>
        </table></td>
      </tr>
<?php
  } else { ?>
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" class="pageHeading"><?php os_header('portfolio_package.gif',HEADING_TITLE); ?> </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_AUTHORS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $authors_query_raw = "select authors_id, authors_name, date_added, last_modified from " . TABLE_AUTHORS . " order by authors_name";
  $authors_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $authors_query_raw, $authors_query_numrows);
  $authors_query = os_db_query($authors_query_raw);
  while ($authors = os_db_fetch_array($authors_query)) {
    if ((!isset($_GET['auID']) || (isset($_GET['auID']) && ($_GET['auID'] == $authors['authors_id']))) && !isset($auInfo) && (substr($action, 0, 3) != 'new')) {
      $author_articles_query = os_db_query("select count(*) as articles_count from " . TABLE_ARTICLES . " where authors_id = '" . (int)$authors['authors_id'] . "'");
      $author_articles = os_db_fetch_array($author_articles_query);

     $auInfo_array = array_merge($authors, $author_articles);
      $auInfo = new objectInfo($auInfo_array);
    }
    $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if (isset($auInfo) && is_object($auInfo) && ($authors['authors_id'] == $auInfo->authors_id)) {
      echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . os_href_link(FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $authors['authors_id'] . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . os_href_link(FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $authors['authors_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $authors['authors_name']; ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($auInfo) && is_object($auInfo) && ($authors['authors_id'] == $auInfo->authors_id)) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="' . os_href_link(FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $authors['authors_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $authors_split->display_count($authors_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_AUTHORS); ?></td>
                    <td class="smallText" align="right"><?php echo $authors_split->display_links($authors_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php
  if (empty($action)) {
?>
              <tr>
                <td align="right" colspan="2" class="smallText"><?php echo '<a class="button" href="' . os_href_link(FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $auInfo->authors_id . '&action=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_AUTHOR . '</b>');

      $contents = array('form' => os_draw_form('authors', FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $auInfo->authors_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $auInfo->authors_name . '</b>');

      if ($auInfo->articles_count > 0) {
        $contents[] = array('text' => '<br>' . os_draw_checkbox_field('delete_articles') . ' ' . TEXT_DELETE_ARTICLES);
        $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_ARTICLES, $auInfo->articles_count));
      }

      $contents[] = array('align' => 'center', 'text' => '<br>' . '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span>' . ' <a class="button" href="' . os_href_link(FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $auInfo->authors_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    default:
      if (isset($auInfo) && is_object($auInfo)) {
        $heading[] = array('text' => '<b>' . $auInfo->authors_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" href="' . os_href_link(FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $auInfo->authors_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" href="' . os_href_link(FILENAME_AUTHORS, 'page=' . $_GET['page'] . '&auID=' . $auInfo->authors_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . os_date_short($auInfo->date_added));
        if (os_not_null($auInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . os_date_short($auInfo->last_modified));
        $contents[] = array('text' => '<br>' . TEXT_ARTICLES . ' ' . $auInfo->articles_count);
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