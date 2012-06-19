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

define('HEADING_TITLE', 'Easy Populate Configuration');
define('EASY_VERSION_A', 'Easy Populate Advanced ');
define('EASY_DEFAULT_LANGUAGE', '  -  Default Language ');
define('EASY_UPLOAD_FILE', 'File uploaded. ');
define('EASY_UPLOAD_TEMP', 'Temporary filename: ');
define('EASY_UPLOAD_USER_FILE', 'User filename: ');
define('EASY_SIZE', 'Size: ');
define('EASY_FILENAME', 'Filename: ');
define('EASY_SPLIT_DOWN', 'You can download your split files in the Tools/Files under /temp/');
define('EASY_UPLOAD_EP_FILE', 'Upload EP File for Import');
define('EASY_SPLIT_EP_FILE', 'Upload and Split a EP File');
define('EASY_INSERT', 'Insert into DB');
define('EASY_SPLIT', 'Upload and Split a EP File');
define('EASY_LIMIT', 'Export:');

define('TEXT_IMPORT_TEMP', 'Import Data from file in %s');
define('TEXT_INSERT_INTO_DB', 'Insert into DB');
define('TEXT_SELECT_ONE', 'Select a EP File for Import');
define('TEXT_SPLIT_FILE', 'Select a EP File');
define('EASY_LABEL_CREATE', 'Create an export file');
define('EASY_LABEL_IMPORT', 'Import:');
define('EASY_LABEL_EXPORT_CHARSET', 'Export file charset: ');
define('EASY_LABEL_IMPORT_CHARSET', 'Import file charset: ');
define('EASY_LABEL_CREATE_SELECT', 'Select method to save export file');
define('EASY_LABEL_CREATE_SAVE', 'Save to temp file on server');
define('EASY_LABEL_SELECT_DOWN', 'Select field set to download');
define('EASY_LABEL_SORT', 'Select field for sort order');
define('EASY_LABEL_PRODUCT_RANGE', 'Limit by Products_ID(s)');
define('EASY_LABEL_LIMIT_CAT', 'Limit By Category');
define('EASY_LABEL_LIMIT_MAN', 'Limit By Manufacturer');

define('EASY_LABEL_PRODUCT_AVAIL', 'Range Available: ');
define('EASY_LABEL_PRODUCT_FROM', ' from ');
define('EASY_LABEL_PRODUCT_TO', ' to ');
define('EASY_LABEL_PRODUCT_RECORDS', '    Total number of records: ');
define('EASY_LABEL_PRODUCT_BEGIN', 'begin: ');
define('EASY_LABEL_PRODUCT_END', 'end: ');
define('EASY_LABEL_PRODUCT_START', 'Start File Creation ');

define('EASY_FILE_LOCATE', 'You can get your file in the Tools/Files under ');
define('EASY_FILE_LOCATE_2', ' by clicking this Link and going to the file manager');
define('EASY_FILE_RETURN', ' You can return to EP by clicking this link.');
define('EASY_IMPORT_TEMP_DIR', 'Import from Temp Dir ');
define('EASY_LABEL_DOWNLOAD', 'Download');
define('EASY_LABEL_COMPLETE', 'Complete');
define('EASY_LABEL_TAB', 'tab-delimited .txt file to edit');
define('EASY_LABEL_MPQ', 'Model/Price/Qty');
define('EASY_LABEL_EP_MC', 'Model/Category');
define('EASY_LABEL_EP_FROGGLE', 'Froogle');
define('EASY_LABEL_EP_ATTRIB', 'Attributes');
define('EASY_LABEL_EXTRA_FIELDS', 'Product Extra Fields');
define('EASY_LABEL_NONE', 'None');
define('EASY_LABEL_CATEGORY', '1st Category Name');
define('PULL_DOWN_MANUFACTURES', 'Manufacturers');
define('EASY_LABEL_PRODUCT', 'Product ID Number');
define('EASY_LABEL_MANUFACTURE', 'Manufacturer ID Number');
define('EASY_LABEL_EP_FROGGLE_HEADER', 'Download a EP or Froogle file');
define('EASY_LABEL_EP_MA', 'Model/Attributes');
define('EASY_LABEL_EP_FR_TITLE', 'Create EP or Froogle Files in Temp Dir ');
define('EASY_LABEL_EP_DOWN_TAB', 'Create <b>Complete</b> tab-delimited .txt file in temp dir');
define('EASY_LABEL_EP_DOWN_MPQ', 'Create <b>Model/Price/Qty</b> tab-delimited .txt file in temp dir');
define('EASY_LABEL_EP_DOWN_MC', 'Create <b>Model/Category</b> tab-delimited .txt file in temp dir');
define('EASY_LABEL_EP_DOWN_MA', 'Create <b>Model/Attributes</b> tab-delimited .txt file in temp dir');
define('EASY_LABEL_EP_DOWN_FROOGLE', 'Create <b>Froogle</b> tab-delimited .txt file in temp dir');

define('EASY_LABEL_NEW_PRODUCT', '!New Product!</font><br>');
define('EASY_LABEL_UPDATED', "<font color='black'> Updated</font><br>");
define('EASY_LABEL_DELETE_STATUS_1', "<font color='red'> !!Deleting product ");
define('EASY_LABEL_DELETE_STATUS_2', " from the database !!</font><br>");
define('EASY_LABEL_LINE_COUNT_1', 'Added ');
define('EASY_LABEL_LINE_COUNT_2', 'records and closing file... ');
define('EASY_LABEL_FILE_COUNT_1', 'Creating file EP_Split ');
define('EASY_LABEL_FILE_COUNT_2', '.txt ...  ');
define('EASY_LABEL_FILE_CLOSE_1', 'Added ');
define('EASY_LABEL_FILE_CLOSE_2', ' records and closing file...');
//errormessages
define('EASY_ERROR_1', 'Strange but there is no default language to work... That may not happen, just in case... ');
define('EASY_ERROR_2', '... ERROR! - Too many characters in the model number.<br>
			25 is the maximum on a standard cre install.<br>
			Your maximum product_model length is set to ');
define('EASY_ERROR_2A', ' <br>You can either shorten your model numbers or increase the size of the field in the database.</font>');
define('EASY_ERROR_2B',  "<font color='red'>");
define('EASY_ERROR_3', '<p class=smallText>No products_id field in record. This line was not imported <br><br>');
define('EASY_ERROR_4', '<font color=red>ERROR - v_customer_group_id and v_customer_price must occur in pairs</font>');
define('EASY_ERROR_5', '</b><font color=red>ERROR - You are trying to use a file created with EP Advanced, please try with Easy Populate Advanced </font>');
define('EASY_ERROR_5a', '<font color=red><b><u>  Click here to return to Easy Populate Basic </u></b></font>');
define('EASY_ERROR_6', '</b><font color=red>ERROR - You are trying to use a file created with EP Basic, please try with Easy Populate Basic </font>');
define('EASY_ERROR_6a', '<font color=red><b><u>  Click here to return to Easy Populate Advanced </u></b></font>');

define('EASY_LABEL_FILE_COUNT_1A', 'Create EPA_Split file ');

?>