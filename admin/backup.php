<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require('includes/top.php');

$breadcrumb->add(HEADING_TITLE, '');

$main->head();
$main->top_menu();
?>

<?php

$TEXT_DUMPER_SUBMIT = TEXT_DUMPER_SUBMIT;
$TEXT_DUMPER_SEC = TEXT_DUMPER_SEC;
$TEXT_DUMPER_DIR_ERROR = TEXT_DUMPER_DIR_ERROR;
$TEXT_DUMPER_DOWNLOAD = TEXT_DUMPER_DOWNLOAD;
$TEXT_DUMPER_BACK = TEXT_DUMPER_BACK;
$TEXT_DUMPER_CREATE = TEXT_DUMPER_CREATE;
$TEXT_DUMPER_NAME_ERROR = TEXT_DUMPER_NAME_ERROR;
$TEXT_DUMPER_CONNECT = TEXT_DUMPER_CONNECT;
$TEXT_DUMPER_CONNECT_ERROR = TEXT_DUMPER_CONNECT_ERROR;
$TEXT_DUMPER_CREATE_FILE = TEXT_DUMPER_CREATE_FILE;
$TEXT_DUMPER_CHARSET_ERROR = TEXT_DUMPER_CHARSET_ERROR;
$TEXT_DUMPER_CHARSET = TEXT_DUMPER_CHARSET;
$TEXT_DUMPER_CHARSET_COLLATION = TEXT_DUMPER_CHARSET_COLLATION;
$TEXT_DUMPER_TABLE = TEXT_DUMPER_TABLE;
$TEXT_DUMPER_CONNECT1 = TEXT_DUMPER_CONNECT1;
$TEXT_DUMPER_PROCESS = TEXT_DUMPER_PROCESS;
$TEXT_DUMPER_MAKE = TEXT_DUMPER_MAKE;
$TEXT_DUMPER_MAKE1 = TEXT_DUMPER_MAKE1;
$TEXT_DUMPER_SIZE  = TEXT_DUMPER_SIZE;
$TEXT_DUMPER_MB  = TEXT_DUMPER_MB;
$TEXT_DUMPER_FILE_SIZE = TEXT_DUMPER_FILE_SIZE;
$TEXT_DUMPER_TABLES_COUNT = TEXT_DUMPER_TABLES_COUNT;
$TEXT_DUMPER_STRING_COUNT = TEXT_DUMPER_STRING_COUNT;
$TEXT_DUMPER_STRING_COUNT = TEXT_DUMPER_STRING_COUNT;
$TEXT_DUMPER_RESTORE = TEXT_DUMPER_RESTORE;
$TEXT_DUMPER_FILE_ERROR = TEXT_DUMPER_FILE_ERROR;
$TEXT_DUMPER_FILE_READ = TEXT_DUMPER_FILE_READ;
$TEXT_DUMPER_FILE_ERROR1 = TEXT_DUMPER_FILE_ERROR1;
$TEXT_DUMPER_QUERY_ERROR = TEXT_DUMPER_QUERY_ERROR;
$TEXT_DUMPER_RESTORED = TEXT_DUMPER_RESTORED;
$TEXT_DUMPER_DATE = TEXT_DUMPER_DATE;
$TEXT_DUMPER_QUERY_COUNT = TEXT_DUMPER_QUERY_COUNT;
$TEXT_DUMPER_TABLES_CREATED = TEXT_DUMPER_TABLES_CREATED;
$TEXT_DUMPER_STRINGS_CREATED = TEXT_DUMPER_STRINGS_CREATED;
$TEXT_DUMPER_MAX = TEXT_DUMPER_MAX;
$TEXT_DUMPER_MED = TEXT_DUMPER_MED;
$TEXT_DUMPER_MIN = TEXT_DUMPER_MIN;
$TEXT_DUMPER_NO = TEXT_DUMPER_NO;

$TEXT_DUMPER_BACKUP = TEXT_DUMPER_BACKUP;
$TEXT_DUMPER_DB = TEXT_DUMPER_DB;
$TEXT_DUMPER_FILTER = TEXT_DUMPER_FILTER;
$TEXT_DUMPER_COMPRESS = TEXT_DUMPER_COMPRESS;
$TEXT_DUMPER_COMPRESS_LEVEL = TEXT_DUMPER_COMPRESS_LEVEL;

$TEXT_DUMPER_RESTORE_DB = TEXT_DUMPER_RESTORE_DB;
$TEXT_DUMPER_FILE = TEXT_DUMPER_FILE;

$TEXT_DUMPER_TABLE_STATUS = TEXT_DUMPER_TABLE_STATUS;
$TEXT_DUMPER_TOTAL_STATUS = TEXT_DUMPER_TOTAL_STATUS;

$TEXT_DUMPER_ERROR = TEXT_DUMPER_ERROR;
$TEXT_DUMPER_BROWSER_ERROR = TEXT_DUMPER_BROWSER_ERROR;

$TEXT_DUMPER_LOGIN_HEADER = TEXT_DUMPER_LOGIN_HEADER;
$TEXT_DUMPER_LOGIN = TEXT_DUMPER_LOGIN;
$TEXT_DUMPER_PASSWORD = TEXT_DUMPER_PASSWORD;

$TEXT_DUMPER_FORBIDDEN = TEXT_DUMPER_FORBIDDEN;
$TEXT_DUMPER_DB_CONNECT = TEXT_DUMPER_DB_CONNECT;
$TEXT_DUMPER_DB_ERROR = TEXT_DUMPER_DB_ERROR;

// Путь и URL к файлам бекапа

define('PATH', 'backups/');
define('URL',  'backups/');

// Максимальное время выполнения скрипта в секундах
// 0 - без ограничений

define('TIME_LIMIT', 600);

// Ограничение размера данных доставаемых за одно обращения к БД (в мегабайтах)
// Нужно для ограничения количества памяти пожираемой сервером при дампе очень объемных таблиц

define('LIMIT', 1);

// mysql сервер

define('DBHOST', DB_SERVER);
//define('DBHOST', 'localhost:3306');

// Базы данных, если сервер не разрешает просматривать список баз данных,
// и ничего не показывается после авторизации. Перечислите названия через запятую

define('DBNAMES', DB_DATABASE);

// Кодировка соединения с MySQL
// auto - автоматический выбор (устанавливается кодировка таблицы), cp1251 - windows-1251, и т.п.

define('CHARSET', 'utf8');

// Кодировка соединения с MySQL при восстановлении
// На случай переноса со старых версий MySQL (до 4.1), у которых не указана кодировка таблиц в дампе
// При добавлении 'forced->', к примеру 'forced->cp1251', кодировка таблиц при восстановлении будет принудительно заменена на cp1251
// Можно также указывать сравнение нужное к примеру 'cp1251_ukrainian_ci' или 'forced->cp1251_ukrainian_ci'

define('RESTORE_CHARSET', 'utf8');

// Включить сохранение настроек и последних действий
// Для отключения установить значение 0

define('SC', 0);

// Типы таблиц у которых сохраняется только структура, разделенные запятой

define('ONLY_CREATE', 'MRG_MyISAM,MERGE,HEAP,MEMORY');

// Глобальная статистика
// Для отключения установить значение 0

define('GS', 0);

// Дальше ничего редактировать не нужно

$is_safe_mode = ini_get('safe_mode') == '1' ? 1 : 0;
if (!$is_safe_mode && function_exists('set_time_limit')) set_time_limit(TIME_LIMIT);


$timer = array_sum(explode(' ', microtime()));
ob_implicit_flush();
error_reporting(E_ALL);

$auth = 0;
$error = '';

if (@mysql_connect(DBHOST, DB_SERVER_USERNAME, DB_SERVER_PASSWORD)){
	$auth = 1;
}
else{
	$error = '#' . mysql_errno() . ': ' . mysql_error();
}

if (!$auth || (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] == 'reload')) {
	setcookie("sxd");
	echo tpl_page(tpl_auth($error ? tpl_error($error) : ''), "<script>if (jsEnabled) {document.write('<span class=\"button\"><button type=\"submit\" value=" . TEXT_DUMPER_SUBMIT . ">" . TEXT_DUMPER_SUBMIT . "</button></span>');}</script>");
	echo "<script>document.getElementById('timer').innerHTML = '" . round(array_sum(explode(' ', microtime())) - $timer, 4) . TEXT_DUMPER_SEC . "'</script>";
	exit;
}
if (!file_exists(PATH) && !$is_safe_mode) {
    mkdir(PATH, 0777) || trigger_error($TEXT_DUMPER_DIR_ERROR, E_USER_ERROR);
}

$SK = new dumper();
define('C_DEFAULT', 1);
define('C_RESULT', 2);
define('C_ERROR', 3);
define('C_WARNING', 4);

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch($action){
	case 'backup':
		$SK->backup();
		break;
	case 'restore':
		$SK->restore();
		break;
	default:
		$SK->main();
}


echo "<script>document.getElementById('timer').innerHTML = '" . round(array_sum(explode(' ', microtime())) - $timer, 4) . TEXT_DUMPER_SEC . "'</script>";

class dumper {
	function dumper() {
		if (file_exists(PATH . "dumper.cfg.php")) {
		    include(PATH . "dumper.cfg.php");
		}
		else{
			$this->SET['last_action'] = 0;
			$this->SET['last_db_backup'] = '';
			$this->SET['tables'] = '';
			$this->SET['comp_method'] = 2;
			$this->SET['comp_level']  = 7;
			$this->SET['last_db_restore'] = '';
		}
		$this->tabs = 0;
		$this->records = 0;
		$this->size = 0;
		$this->comp = 0;

		// Версия MySQL вида 40101
		preg_match("/^(\d+)\.(\d+)\.(\d+)/", mysql_get_server_info(), $m);
		$this->mysql_version = sprintf("%d%02d%02d", $m[1], $m[2], $m[3]);

		$this->only_create = explode(',', ONLY_CREATE);
		$this->forced_charset  = false;
		$this->restore_charset = $this->restore_collate = '';
		if (preg_match("/^(forced->)?(([a-z0-9]+)(\_\w+)?)$/", RESTORE_CHARSET, $matches)) {
			$this->forced_charset  = $matches[1] == 'forced->';
			$this->restore_charset = $matches[3];
			$this->restore_collate = !empty($matches[4]) ? ' COLLATE ' . $matches[2] : '';
		}
	}

	function backup() {
	
$TEXT_DUMPER_SUBMIT = TEXT_DUMPER_SUBMIT;
$TEXT_DUMPER_SEC = TEXT_DUMPER_SEC;
$TEXT_DUMPER_DIR_ERROR = TEXT_DUMPER_DIR_ERROR;
$TEXT_DUMPER_DOWNLOAD = TEXT_DUMPER_DOWNLOAD;
$TEXT_DUMPER_BACK = TEXT_DUMPER_BACK;
$TEXT_DUMPER_CREATE = TEXT_DUMPER_CREATE;
$TEXT_DUMPER_NAME_ERROR = TEXT_DUMPER_NAME_ERROR;
$TEXT_DUMPER_CONNECT = TEXT_DUMPER_CONNECT;
$TEXT_DUMPER_CONNECT_ERROR = TEXT_DUMPER_CONNECT_ERROR;
$TEXT_DUMPER_CREATE_FILE = TEXT_DUMPER_CREATE_FILE;
$TEXT_DUMPER_CHARSET_ERROR = TEXT_DUMPER_CHARSET_ERROR;
$TEXT_DUMPER_CHARSET = TEXT_DUMPER_CHARSET;
$TEXT_DUMPER_CHARSET_COLLATION = TEXT_DUMPER_CHARSET_COLLATION;
$TEXT_DUMPER_TABLE = TEXT_DUMPER_TABLE;
$TEXT_DUMPER_CONNECT1 = TEXT_DUMPER_CONNECT1;
$TEXT_DUMPER_PROCESS = TEXT_DUMPER_PROCESS;
$TEXT_DUMPER_TABLES_COUNT = TEXT_DUMPER_TABLES_COUNT;
$TEXT_DUMPER_STRING_COUNT = TEXT_DUMPER_STRING_COUNT;
$TEXT_DUMPER_MAKE = TEXT_DUMPER_MAKE;
$TEXT_DUMPER_MAKE1 = TEXT_DUMPER_MAKE1;
$TEXT_DUMPER_SIZE  = TEXT_DUMPER_SIZE;
$TEXT_DUMPER_MB  = TEXT_DUMPER_MB;
$TEXT_DUMPER_FILE_SIZE = TEXT_DUMPER_FILE_SIZE;
$TEXT_DUMPER_STRING_COUNT = TEXT_DUMPER_STRING_COUNT;
$TEXT_DUMPER_STRING_COUNT = TEXT_DUMPER_STRING_COUNT;
	
		if (!isset($_REQUEST)) {$this->main();}
		set_error_handler("SXD_errorHandler");
		$buttons = "<a id=save href='' style='display: none;'>" . TEXT_DUMPER_DOWNLOAD . "</a> &nbsp; <input id=back type=button class=button value='" . TEXT_DUMPER_BACK . "' disabled onclick=\"history.back();\">";
		echo tpl_page(tpl_process($TEXT_DUMPER_CREATE), $buttons);

		$this->SET['last_action']     = 0;
		$this->SET['last_db_backup']  = isset($_REQUEST['db_backup']) ? $_REQUEST['db_backup'] : '';
		$this->SET['tables_exclude']  = !empty($_REQUEST['tables']) && $_REQUEST['tables']{0} == '^' ? 1 : 0;
		$this->SET['tables']          = isset($_REQUEST['tables']) ? $_REQUEST['tables'] : '';
		$this->SET['comp_method']     = isset($_REQUEST['comp_method']) ? intval($_REQUEST['comp_method']) : 0;
		$this->SET['comp_level']      = isset($_REQUEST['comp_level']) ? intval($_REQUEST['comp_level']) : 0;
		$this->fn_save();

		$this->SET['tables']          = explode(",", $this->SET['tables']);
		if (!empty($_REQUEST['tables'])) {
		    foreach($this->SET['tables'] AS $table){
    			$table = preg_replace("/[^\w*?^]/", "", $table);
				$pattern = array( "/\?/", "/\*/");
				$replace = array( ".", ".*?");
				$tbls[] = preg_replace($pattern, $replace, $table);
    		}
		}
		else{
			$this->SET['tables_exclude'] = 1;
		}

		if ($this->SET['comp_level'] == 0) {
		    $this->SET['comp_method'] = 0;
		}
		$db = $this->SET['last_db_backup'];

		if (!$db) {
			echo tpl_l($TEXT_DUMPER_NAME_ERROR, C_ERROR);
			echo tpl_enableBack();
		    exit;
		}
		echo tpl_l($TEXT_DUMPER_CONNECT . "`{$db}`.");
		mysql_select_db($db) or trigger_error ($TEXT_DUMPER_CONNECT_ERROR . "<br />" . mysql_error(), E_USER_ERROR);
		$tables = array();
        $result = mysql_query("SHOW tableS");
		$all = 0;
        while($row = mysql_fetch_array($result)) {
			$status = 0;
			if (!empty($tbls)) {
			    foreach($tbls AS $table){
    				$exclude = preg_match("/^\^/", $table) ? true : false;
    				if (!$exclude) {
    					if (preg_match("/^{$table}$/i", $row[0])) {
    					    $status = 1;
    					}
    					$all = 1;
    				}
    				if ($exclude && preg_match("/{$table}$/i", $row[0])) {
    				    $status = -1;
    				}
    			}
			}
			else {
				$status = 1;
			}
			if ($status >= $all) {
    			$tables[] = $row[0];
    		}
        }

		$tabs = count($tables);
		// Определение размеров таблиц
		$result = mysql_query("SHOW table STATUS");
		$tabinfo = array();
		$tab_charset = array();
		$tab_type = array();
		$tabinfo[0] = 0;
		$info = '';
		while($item = mysql_fetch_assoc($result)){
			if(in_array($item['Name'], $tables)) {
				$item['Rows'] = empty($item['Rows']) ? 0 : $item['Rows'];
				$tabinfo[0] += $item['Rows'];
				$tabinfo[$item['Name']] = $item['Rows'];
				$this->size += $item['Data_length'];
				$tabsize[$item['Name']] = 1 + round(LIMIT * 1048576 / ($item['Avg_row_length'] + 1));
				if($item['Rows']) $info .= "|" . $item['Rows'];
				if (!empty($item['Collation']) && preg_match("/^([a-z0-9]+)_/i", $item['Collation'], $m)) {
					$tab_charset[$item['Name']] = $m[1];
				}
				$tab_type[$item['Name']] = isset($item['Engine']) ? $item['Engine'] : $item['Type'];
			}
		}
		$show = 10 + $tabinfo[0] / 50;
		$info = $tabinfo[0] . $info;
		$name = $db . '_' . date("Y-m-d_H-i");
        $fp = $this->fn_open($name, "w");
		echo tpl_l($TEXT_DUMPER_CREATE_FILE . "<br />\\n  -  {$this->filename}");
		$this->fn_write($fp, "#SKD101|{$db}|{$tabs}|" . date("Y.m.d H:i:s") ."|{$info}\n\n");
		$t=0;
		echo tpl_l(str_repeat("-", 60));
		$result = mysql_query("SET SQL_QUOTE_SHOW_CREATE = 1");
		// Кодировка соединения по умолчанию
		if ($this->mysql_version > 40101 && CHARSET != 'auto') {
			mysql_query("SET NAMES '" . CHARSET . "'") or trigger_error ($TEXT_DUMPER_CHARSET_ERROR . "<br />" . mysql_error(), E_USER_ERROR);
			$last_charset = CHARSET;
		}
		else{
			$last_charset = '';
		}
        foreach ($tables AS $table){
			// Выставляем кодировку соединения соответствующую кодировке таблицы
			if ($this->mysql_version > 40101 && $tab_charset[$table] != $last_charset) {
				if (CHARSET == 'auto') {
					mysql_query("SET NAMES '" . $tab_charset[$table] . "'") or trigger_error ($TEXT_DUMPER_CHARSET_ERROR . "<br />" . mysql_error(), E_USER_ERROR);
					echo tpl_l($TEXT_DUMPER_CHARSET . "`" . $tab_charset[$table] . "`.", C_WARNING);
					$last_charset = $tab_charset[$table];
				}
				else{
					echo tpl_l($TEXT_DUMPER_CHARSET_COLLATION, C_ERROR);
					echo tpl_l($TEXT_DUMPER_TABLE . '`'. $table .'` -> ' . $tab_charset[$table] . ' (' . $TEXT_DUMPER_CONNECT . ''  . CHARSET . ')', C_ERROR);
				}
			}
			echo tpl_l($TEXT_DUMPER_PROCESS . "`{$table}` [" . fn_int($tabinfo[$table]) . "].");
        	// Создание таблицы
			$result = mysql_query("SHOW CREATE table `{$table}`");
        	$tab = mysql_fetch_array($result);
			$tab = preg_replace('/(default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP|DEFAULT CHARSET=\w+|COLLATE=\w+|character set \w+|collate \w+)/i', '/*!40101 \\1 */', $tab);
        	$this->fn_write($fp, "DROP table IF EXISTS `{$table}`;\n{$tab[1]};\n\n");
        	// Проверяем нужно ли дампить данные
        	if (in_array($tab_type[$table], $this->only_create)) {
				continue;
			}
        	// Опредеделяем типы столбцов
            $NumericColumn = array();
            $result = mysql_query("SHOW COLUMNS FROM `{$table}`");
            $field = 0;
            while($col = mysql_fetch_row($result)) {
            	$NumericColumn[$field++] = preg_match("/^(\w*int|year)/", $col[1]) ? 1 : 0;
            }
			$fields = $field;
            $from = 0;
			$limit = $tabsize[$table];
			$limit2 = round($limit / 3);
			if ($tabinfo[$table] > 0) {
			if ($tabinfo[$table] > $limit2) {
			    echo tpl_s(0, $t / $tabinfo[0]);
			}
			$i = 0;
			$this->fn_write($fp, "INSERT INTO `{$table}` VALUES");
            while(($result = mysql_query("SELECT * FROM `{$table}` LIMIT {$from}, {$limit}")) && ($total = mysql_num_rows($result))){
            		while($row = mysql_fetch_row($result)) {
                    	$i++;
    					$t++;

						for($k = 0; $k < $fields; $k++){
                    		if ($NumericColumn[$k])
                    		    $row[$k] = isset($row[$k]) ? $row[$k] : "NULL";
                    		else
                    			$row[$k] = isset($row[$k]) ? "'" . mysql_escape_string($row[$k]) . "'" : "NULL";
                    	}

    					$this->fn_write($fp, ($i == 1 ? "" : ",") . "\n(" . implode(", ", $row) . ")");
    					if ($i % $limit2 == 0)
    						echo tpl_s($i / $tabinfo[$table], $t / $tabinfo[0]);
               		}
					mysql_free_result($result);
					if ($total < $limit) {
					    break;
					}
    				$from += $limit;
            }

			$this->fn_write($fp, ";\n\n");
    		echo tpl_s(1, $t / $tabinfo[0]);}
		}
		$this->tabs = $tabs;
		$this->records = $tabinfo[0];
		$this->comp = $this->SET['comp_method'] * 10 + $this->SET['comp_level'];
        echo tpl_s(1, 1);
        echo tpl_l(str_repeat("-", 60));
        $this->fn_close($fp);
		echo tpl_l($TEXT_DUMPER_MAKE . "`{$db}`" . $TEXT_DUMPER_MAKE1, C_RESULT);
		echo tpl_l($TEXT_DUMPER_SIZE . round($this->size / 1048576, 2) . $TEXT_DUMPER_MB, C_RESULT);
		$filesize = round(filesize(PATH . $this->filename) / 1048576, 2) . $TEXT_DUMPER_MB;
		echo tpl_l($TEXT_DUMPER_FILE_SIZE . "{$filesize}", C_RESULT);
		echo tpl_l($TEXT_DUMPER_TABLES_COUNT . "{$tabs}", C_RESULT);
		echo tpl_l($TEXT_DUMPER_STRING_COUNT . fn_int($tabinfo[0]), C_RESULT);
		echo "<script>with (document.getElementById('save')) {style.display = ''; innerHTML = '" . TEXT_DUMPER_DOWNLOAD . " ({$filesize})'; href = '" . os_href_link(FILENAME_FILE, 'path=backups/' . $this->filename) . "'; }document.getElementById('back').disabled = 0;</script>";
		
		
		// Передача данных для глобальной статистики
		if (GS) echo "<script>document.getElementById('GS').src = 'http://sypex.net/gs.php?b={$this->tabs},{$this->records},{$this->size},{$this->comp},108';</script>";

	}

	function restore(){
	
$TEXT_DUMPER_BACK = TEXT_DUMPER_BACK;
$TEXT_DUMPER_NAME_ERROR = TEXT_DUMPER_NAME_ERROR;
$TEXT_DUMPER_CONNECT = TEXT_DUMPER_CONNECT;
$TEXT_DUMPER_CONNECT_ERROR = TEXT_DUMPER_CONNECT_ERROR;
$TEXT_DUMPER_CREATE_FILE = TEXT_DUMPER_CREATE_FILE;
$TEXT_DUMPER_CHARSET_ERROR = TEXT_DUMPER_CHARSET_ERROR;
$TEXT_DUMPER_CHARSET = TEXT_DUMPER_CHARSET;
$TEXT_DUMPER_CHARSET_COLLATION = TEXT_DUMPER_CHARSET_COLLATION;
$TEXT_DUMPER_TABLE = TEXT_DUMPER_TABLE;
$TEXT_DUMPER_CONNECT1 = TEXT_DUMPER_CONNECT1;
$TEXT_DUMPER_PROCESS = TEXT_DUMPER_PROCESS;
$TEXT_DUMPER_TABLES_COUNT = TEXT_DUMPER_TABLES_COUNT;
$TEXT_DUMPER_STRING_COUNT = TEXT_DUMPER_STRING_COUNT;
$TEXT_DUMPER_RESTORE = TEXT_DUMPER_RESTORE;
$TEXT_DUMPER_FILE_ERROR = TEXT_DUMPER_FILE_ERROR;
$TEXT_DUMPER_FILE_READ = TEXT_DUMPER_FILE_READ;
$TEXT_DUMPER_FILE_ERROR1 = TEXT_DUMPER_FILE_ERROR1;
$TEXT_DUMPER_QUERY_ERROR = TEXT_DUMPER_QUERY_ERROR;
$TEXT_DUMPER_RESTORED = TEXT_DUMPER_RESTORED;
$TEXT_DUMPER_DATE = TEXT_DUMPER_DATE;
$TEXT_DUMPER_QUERY_COUNT = TEXT_DUMPER_QUERY_COUNT;
$TEXT_DUMPER_TABLES_CREATED = TEXT_DUMPER_TABLES_CREATED;
$TEXT_DUMPER_STRINGS_CREATED = TEXT_DUMPER_STRINGS_CREATED;
$TEXT_DUMPER_MAX = TEXT_DUMPER_MAX;
$TEXT_DUMPER_MED = TEXT_DUMPER_MED;
$TEXT_DUMPER_MIN = TEXT_DUMPER_MIN;
$TEXT_DUMPER_NO = TEXT_DUMPER_NO;

		if (!isset($_REQUEST)) {$this->main();}
		set_error_handler("SXD_errorHandler");
		$buttons = "<input id=back type=button class=button value='" . TEXT_DUMPER_BACK . "' disabled onclick=\"history.back();\">";
		echo tpl_page(tpl_process($TEXT_DUMPER_RESTORE), $buttons);

		$this->SET['last_action']     = 1;
		$this->SET['last_db_restore'] = isset($_REQUEST['db_restore']) ? $_REQUEST['db_restore'] : '';
		$file						  = isset($_POST['file']) ? $_POST['file'] : '';
		$this->fn_save();
		$db = $this->SET['last_db_restore'];

		if (!$db) {
			echo tpl_l($TEXT_DUMPER_NAME_ERROR, C_ERROR);
			echo tpl_enableBack();
		    exit;
		}
		echo tpl_l($TEXT_DUMPER_CONNECT . "`{$db}`.");
		mysql_select_db($db) or trigger_error ($TEXT_DUMPER_CONNECT_ERROR . "<br />" . mysql_error(), E_USER_ERROR);

		// Определение формата файла
		if(preg_match("/^(.+?)\.sql(\.(bz2|gz))?$/", $file, $matches)) {
			if (isset($matches[3]) && $matches[3] == 'bz2') {
			    $this->SET['comp_method'] = 2;
			}
			elseif (isset($matches[2]) &&$matches[3] == 'gz'){
				$this->SET['comp_method'] = 1;
			}
			else{
				$this->SET['comp_method'] = 0;
			}
			$this->SET['comp_level'] = '';
			if (!file_exists(PATH . "/{$file}")) {
    		    echo tpl_l($TEXT_DUMPER_FILE_ERROR, C_ERROR);
				echo tpl_enableBack();
    		    exit;
    		}
			echo tpl_l($TEXT_DUMPER_FILE_READ . "`{$file}`.");
			$file = $matches[1];
		}
		else{
			echo tpl_l($TEXT_DUMPER_FILE_ERROR1, C_ERROR);
			echo tpl_enableBack();
		    exit;
		}
		echo tpl_l(str_repeat("-", 60));
		$fp = $this->fn_open($file, "r");
		$this->file_cache = $sql = $table = $insert = '';
        $is_skd = $query_len = $execute = $q =$t = $i = $aff_rows = 0;
		$limit = 300;
        $index = 4;
		$tabs = 0;
		$cache = '';
		$info = array();

		// Установка кодировки соединения
		if ($this->mysql_version > 40101 && (CHARSET != 'auto' || $this->forced_charset)) { // Кодировка по умолчанию, если в дампе не указана кодировка
			mysql_query("SET NAMES '" . $this->restore_charset . "'") or trigger_error ($TEXT_DUMPER_CHARSET_ERROR . "<br />" . mysql_error(), E_USER_ERROR);
			echo tpl_l($TEXT_DUMPER_CHARSET . "`" . $this->restore_charset . "`.", C_WARNING);
			$last_charset = $this->restore_charset;
		}
		else {
			$last_charset = '';
		}
		$last_showed = '';
		while(($str = $this->fn_read_str($fp)) !== false){
			if (empty($str) || preg_match("/^(#|--)/", $str)) {
				if (!$is_skd && preg_match("/^#SKD101\|/", $str)) {
				    $info = explode("|", $str);
					echo tpl_s(0, $t / $info[4]);
					$is_skd = 1;
				}
        	    continue;
        	}
			$query_len += strlen($str);

			if (!$insert && preg_match("/^(INSERT INTO `?([^` ]+)`? .*?VALUES)(.*)$/i", $str, $m)) {
				if ($table != $m[2]) {
				    $table = $m[2];
					$tabs++;
					$cache .= tpl_l("Таблица `{$table}`.");
					$last_showed = $table;
					$i = 0;
					if ($is_skd)
					    echo tpl_s(100 , $t / $info[4]);
				}
        	    $insert = $m[1] . ' ';
				$sql .= $m[3];
				$index++;
				$info[$index] = isset($info[$index]) ? $info[$index] : 0;
				$limit = round($info[$index] / 20);
				$limit = $limit < 300 ? 300 : $limit;
				if ($info[$index] > $limit){
					echo $cache;
					$cache = '';
					echo tpl_s(0 / $info[$index], $t / $info[4]);
				}
        	}
			else{
        		$sql .= $str;
				if ($insert) {
				    $i++;
    				$t++;
    				if ($is_skd && $info[$index] > $limit && $t % $limit == 0){
    					echo tpl_s($i / $info[$index], $t / $info[4]);
    				}
				}
        	}

			if (!$insert && preg_match("/^CREATE table (IF NOT EXISTS )?`?([^` ]+)`?/i", $str, $m) && $table != $m[2]){
				$table = $m[2];
				$insert = '';
				$tabs++;
				$is_create = true;
				$i = 0;
			}
			if ($sql) {
			    if (preg_match("/;$/", $str)) {
            		$sql = rtrim($insert . $sql, ";");
					if (empty($insert)) {
						if ($this->mysql_version < 40101) {
				    		$sql = preg_replace("/ENGINE\s?=/", "type=", $sql);
						}
						elseif (preg_match("/CREATE table/i", $sql)){
							// Выставляем кодировку соединения
							if (preg_match("/(CHARACTER SET|CHARSET)[=\s]+(\w+)/i", $sql, $charset)) {
								if (!$this->forced_charset && $charset[2] != $last_charset) {
									if (CHARSET == 'auto') {
										mysql_query("SET NAMES '" . $charset[2] . "'") or trigger_error ($TEXT_DUMPER_CHARSET_ERROR . "<br />{$sql}<br />" . mysql_error(), E_USER_ERROR);
										$cache .= tpl_l($TEXT_DUMPER_CHARSET . "`" . $charset[2] . "`.", C_WARNING);
										$last_charset = $charset[2];
									}
									else{
										$cache .= tpl_l($TEXT_DUMPER_CHARSET_COLLATION, C_ERROR);
										$cache .= tpl_l($TEXT_DUMPER_TABLE . '`'. $table .'` -> ' . $charset[2] . ' (' . $TEXT_DUMPER_CONNECT1 . $this->restore_charset . ')', C_ERROR);
									}
								}
								// Меняем кодировку если указано форсировать кодировку
								if ($this->forced_charset) {
									$sql = preg_replace("/(\/\*!\d+\s)?((COLLATE)[=\s]+)\w+(\s+\*\/)?/i", '', $sql);
									$sql = preg_replace("/((CHARACTER SET|CHARSET)[=\s]+)\w+/i", "\\1" . $this->restore_charset . $this->restore_collate, $sql);
								}
							}
							elseif(CHARSET == 'auto'){ // Вставляем кодировку для таблиц, если она не указана и установлена auto кодировка
								$sql .= ' DEFAULT CHARSET=' . $this->restore_charset . $this->restore_collate;
								if ($this->restore_charset != $last_charset) {
									mysql_query("SET NAMES '" . $this->restore_charset . "'") or trigger_error ($TEXT_DUMPER_CHARSET_ERROR . "<br />{$sql}<br />" . mysql_error(), E_USER_ERROR);
									$cache .= tpl_l($TEXT_DUMPER_CHARSET . "`" . $this->restore_charset . "`.", C_WARNING);
									$last_charset = $this->restore_charset;
								}
							}
						}
						if ($last_showed != $table) {$cache .= tpl_l($TEXT_DUMPER_TABLE . "`{$table}`."); $last_showed = $table;}
					}
					elseif($this->mysql_version > 40101 && empty($last_charset)) { // Устанавливаем кодировку на случай если отсутствует CREATE table
						mysql_query("SET $this->restore_charset '" . $this->restore_charset . "'") or trigger_error (TEXT_DUMPER_CHARSET_ERROR . "<br />{$sql}<br />" . mysql_error(), E_USER_ERROR);
						echo tpl_l($TEXT_DUMPER_CHARSET . "`" . $this->restore_charset . "`.", C_WARNING);
						$last_charset = $this->restore_charset;
					}
            		$insert = '';
            	    $execute = 1;
            	}
            	if ($query_len >= 65536 && preg_match("/,$/", $str)) {
            		$sql = rtrim($insert . $sql, ",");
            	    $execute = 1;
            	}
    			if ($execute) {
            		$q++;
            		mysql_query($sql) or trigger_error (TEXT_DUMPER_QUERY_ERROR . "<br />" . mysql_error(), E_USER_ERROR);
					if (preg_match("/^insert/i", $sql)) {
            		    $aff_rows += mysql_affected_rows();
            		}
            		$sql = '';
            		$query_len = 0;
            		$execute = 0;
            	}
			}
		}
		echo $cache;
		echo tpl_s(1 , 1);
		echo tpl_l(str_repeat("-", 60));
		echo tpl_l($TEXT_DUMPER_RESTORED, C_RESULT);
		if (isset($info[3])) echo tpl_l($TEXT_DUMPER_DATE . "{$info[3]}", C_RESULT);
		echo tpl_l($TEXT_DUMPER_QUERY_COUNT . "{$q}", C_RESULT);
		echo tpl_l($TEXT_DUMPER_TABLES_CREATED . "{$tabs}", C_RESULT);
		echo tpl_l(TEXT_DUMPER_STRINGS_CREATED . "{$aff_rows}", C_RESULT);

		$this->tabs = $tabs;
		$this->records = $aff_rows;
		$this->size = filesize(PATH . $this->filename);
		$this->comp = $this->SET['comp_method'] * 10 + $this->SET['comp_level'];
		echo "<script>document.getElementById('back').disabled = 0;</script>";
		// Передача данных для глобальной статистики
		if (GS) echo "<script>document.getElementById('GS').src = 'http://sypex.net/gs.php?r={$this->tabs},{$this->records},{$this->size},{$this->comp},108';</script>";

		$this->fn_close($fp);
	}

	function main(){
		$this->comp_levels = array('9' => TEXT_DUMPER_MAX, '8' => '8', '7' => '7', '6' => '6', '5' => TEXT_DUMPER_MED, '4' => '4', '3' => '3', '2' => '2', '1' => TEXT_DUMPER_MIN,'0' => TEXT_DUMPER_NO);

		if (function_exists("bzopen")) {
		    $this->comp_methods[2] = 'BZip2';
		}
		if (function_exists("gzopen")) {
		    $this->comp_methods[1] = 'GZip';
		}
		$this->comp_methods[0] = TEXT_DUMPER_NO;
		if (count($this->comp_methods) == 1) {
		    $this->comp_levels = array('0' =>TEXT_DUMPER_NO);
		}

		$dbs = $this->db_select();
		$this->vars['db_backup']    = $this->fn_select($dbs, $this->SET['last_db_backup']);
		$this->vars['db_restore']   = $this->fn_select($dbs, $this->SET['last_db_restore']);
		$this->vars['comp_levels']  = $this->fn_select($this->comp_levels, $this->SET['comp_level']);
		$this->vars['comp_methods'] = $this->fn_select($this->comp_methods, $this->SET['comp_method']);
		$this->vars['tables']       = $this->SET['tables'];
		$this->vars['files']        = $this->fn_select($this->file_select(), '');
		$buttons = "<span class=\"button\"><button type=\"submit\" value=" . TEXT_DUMPER_SUBMIT . ">" . TEXT_DUMPER_SUBMIT . "</button></span>";
		echo tpl_page(tpl_main(), $buttons);
	}

	function db_select(){
		if (DBNAMES != '') {
			$items = explode(',', trim(DBNAMES));
			foreach($items AS $item){
    			if (mysql_select_db($item)) {
    				$tables = mysql_query("SHOW tableS");
    				if ($tables) {
    	  			    $tabs = mysql_num_rows($tables);
    	  				$dbs[$item] = "{$item} ({$tabs})";
    	  			}
    			}
			}
		}
		else {
    		$result = mysql_query("SHOW DATABASES");
    		$dbs = array();
    		while($item = mysql_fetch_array($result)){
    			if (mysql_select_db($item[0])) {
    				$tables = mysql_query("SHOW tableS");
    				if ($tables) {
    	  			    $tabs = mysql_num_rows($tables);
    	  				$dbs[$item[0]] = "{$item[0]} ({$tabs})";
    	  			}
    			}
    		}
		}
	    return $dbs;
	}

	function file_select(){
		$files = array('' => ' ');
		if (is_dir(PATH) && $handle = opendir(PATH)) {
            while (false !== ($file = readdir($handle))) {
                if (preg_match("/^.+?\.sql(\.(gz|bz2))?$/", $file)) {
                    $files[$file] = $file;
                }
            }
            closedir($handle);
        }
        ksort($files);
		return $files;
	}

	function fn_open($name, $mode){
		if ($this->SET['comp_method'] == 2) {
			$this->filename = "{$name}.sql.bz2";
		    return bzopen(PATH . $this->filename, "{$mode}b{$this->SET['comp_level']}");
		}
		elseif ($this->SET['comp_method'] == 1) {
			$this->filename = "{$name}.sql.gz";
		    return gzopen(PATH . $this->filename, "{$mode}b{$this->SET['comp_level']}");
		}
		else{
			$this->filename = "{$name}.sql";
			return fopen(PATH . $this->filename, "{$mode}b");
		}
	}

	function fn_write($fp, $str){
		if ($this->SET['comp_method'] == 2) {
		    bzwrite($fp, $str);
		}
		elseif ($this->SET['comp_method'] == 1) {
		    gzwrite($fp, $str);
		}
		else{
			fwrite($fp, $str);
		}
	}

	function fn_read($fp){
		if ($this->SET['comp_method'] == 2) {
		    return bzread($fp, 4096);
		}
		elseif ($this->SET['comp_method'] == 1) {
		    return gzread($fp, 4096);
		}
		else{
			return fread($fp, 4096);
		}
	}

	function fn_read_str($fp){
		$string = '';
		$this->file_cache = ltrim($this->file_cache);
		$pos = strpos($this->file_cache, "\n", 0);
		if ($pos < 1) {
			while (!$string && ($str = $this->fn_read($fp))){
    			$pos = strpos($str, "\n", 0);
    			if ($pos === false) {
    			    $this->file_cache .= $str;
    			}
    			else{
    				$string = $this->file_cache . substr($str, 0, $pos);
    				$this->file_cache = substr($str, $pos + 1);
    			}
    		}
			if (!$str) {
			    if ($this->file_cache) {
					$string = $this->file_cache;
					$this->file_cache = '';
				    return trim($string);
				}
			    return false;
			}
		}
		else {
  			$string = substr($this->file_cache, 0, $pos);
  			$this->file_cache = substr($this->file_cache, $pos + 1);
		}
		return trim($string);
	}

	function fn_close($fp){
		if ($this->SET['comp_method'] == 2) {
		    bzclose($fp);
		}
		elseif ($this->SET['comp_method'] == 1) {
		    gzclose($fp);
		}
		else{
			fclose($fp);
		}
		@chmod(PATH . $this->filename, 0666);
		$this->fn_index();
	}

	function fn_select($items, $selected){
		$select = '';
		foreach($items AS $key => $value){
			$select .= $key == $selected ? "<option value='{$key}' selected>{$value}" : "<option value='{$key}'>{$value}";
		}
		return $select;
	}

	function fn_save(){
		if (SC) {
			$ne = !file_exists(PATH . "dumper.cfg.php");
		    $fp = fopen(PATH . "dumper.cfg.php", "wb");
        	fwrite($fp, "<?php\n\$this->SET = " . fn_arr2str($this->SET) . "\n?>");
        	fclose($fp);
			if ($ne) @chmod(PATH . "dumper.cfg.php", 0666);
			$this->fn_index();
		}
	}

	function fn_index(){
		if (!file_exists(PATH . 'index.html')) {
		    $fh = fopen(PATH . 'index.html', 'wb');
			fwrite($fh, tpl_backup_index());
			fclose($fh);
			@chmod(PATH . 'index.html', 0666);
		}
	}
}

function fn_int($num){
	return number_format($num, 0, ',', ' ');
}

function fn_arr2str($array) {
	$str = "array(\n";
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			$str .= "'$key' => " . fn_arr2str($value) . ",\n\n";
		}
		else {
			$str .= "'$key' => '" . str_replace("'", "\'", $value) . "',\n";
		}
	}
	return $str . ")";
}

// Шаблоны

function tpl_page($content = '', $buttons = ''){

return <<<HTML

<table height="100%" border="0" cellspacing="1" cellpadding="0">
<tr>
<form name="skb" method="post" action="backup.php">
<td valign="top" style="padding: 8px 8px;">
{$content}
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr>
<td style="color: #CECECE"></td>
<td align="right">{$buttons}</td>
</tr>
</table></td>
</form>
</tr>
</table>

</td>
</tr>
</table>

HTML;
}

function tpl_main(){

$TEXT_DUMPER_BACKUP = TEXT_DUMPER_BACKUP;
$TEXT_DUMPER_DB = TEXT_DUMPER_DB;
$TEXT_DUMPER_FILTER = TEXT_DUMPER_FILTER;
$TEXT_DUMPER_COMPRESS = TEXT_DUMPER_COMPRESS;
$TEXT_DUMPER_COMPRESS_LEVEL = TEXT_DUMPER_COMPRESS_LEVEL;

$TEXT_DUMPER_RESTORE_DB = TEXT_DUMPER_RESTORE_DB;
$TEXT_DUMPER_FILE = TEXT_DUMPER_FILE;

global $SK;
return <<<HTML
<fieldset onclick="document.skb.action[0].checked = 1;" style="border: 1px solid #eaeaea;">
<legend>
<input type="radio" name="action" value="backup">
$TEXT_DUMPER_BACKUP</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr>
<td width="35%">$TEXT_DUMPER_DB</td>
<td width="65%"><select name="db_backup">
{$SK->vars['db_backup']}
</select></td>
</tr>
<tr>
<td>$TEXT_DUMPER_FILTER</td>
<td><input name="tables" type="text" class="text" value="{$SK->vars['tables']}"></td>
</tr>
<tr>
<td>$TEXT_DUMPER_COMPRESS</td>
<td><select name="comp_method">
{$SK->vars['comp_methods']}
</select></td>
</tr>
<tr>
<td>$TEXT_DUMPER_COMPRESS_LEVEL</td>
<td><select name="comp_level">
{$SK->vars['comp_levels']}
</select></td>
</tr>
</table>
</fieldset>
<fieldset onclick="document.skb.action[1].checked = 1;" style="border: 1px solid #eaeaea;">
<legend>
<input type="radio" name="action" value="restore">
$TEXT_DUMPER_RESTORE_DB</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr>
<td>$TEXT_DUMPER_DB</td>
<td><select name="db_restore">
{$SK->vars['db_restore']}
</select></td>
</tr>
<tr>
<td width="35%">$TEXT_DUMPER_FILE</td>
<td width="65%"><select name="file">
{$SK->vars['files']}
</select></td>
</tr>
</table>
</fieldset>
</span>
<script>
document.skb.action[{$SK->SET['last_action']}].checked = 1;
</script>

HTML;
}

function tpl_process($title){

$TEXT_DUMPER_TABLE_STATUS = TEXT_DUMPER_TABLE_STATUS;
$TEXT_DUMPER_TOTAL_STATUS = TEXT_DUMPER_TOTAL_STATUS;

return <<<HTML
<fieldset>
<legend>{$title}&nbsp;</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr><td colspan="2"><div id="logarea" style="width: 100%; height: 140px; border: 1px solid #7F9DB9; padding: 3px; overflow: auto;"></div></td></tr>
<tr><td width="31%">$TEXT_DUMPER_TABLE_STATUS</td><td width="69%"><table width="100%" border="1" cellpadding="0" cellspacing="0">
<tr><td bgcolor="#FFFFFF"><table width="1" border="0" cellpadding="0" cellspacing="0" bgcolor="#5555CC" id="st_tab"
style="FILTER: progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#CCCCFF,endColorStr=#5555CC);
border-right: 1px solid #AAAAAA"><tr><td height="12"></td></tr></table></td></tr></table></td></tr>
<tr><td>$TEXT_DUMPER_TOTAL_STATUS</td><td><table width="100%" border="1" cellspacing="0" cellpadding="0">
<tr><td bgcolor="#FFFFFF"><table width="1" border="0" cellpadding="0" cellspacing="0" bgcolor="#00AA00" id="so_tab"
style="FILTER: progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#CCFFCC,endColorStr=#00AA00);
border-right: 1px solid #AAAAAA"><tr><td height="12"></td></tr></table></td>
</tr></table></td></tr></table>
</fieldset>
<script>
var WidthLocked = false;
function s(st, so){
	document.getElementById('st_tab').width = st ? st + '%' : '1';
	document.getElementById('so_tab').width = so ? so + '%' : '1';
}
function l(str, color){
	switch(color){
		case 2: color = 'navy'; break;
		case 3: color = 'red'; break;
		case 4: color = 'maroon'; break;
		default: color = 'black';
	}
	with(document.getElementById('logarea')){
		if (!WidthLocked){
			style.width = clientWidth;
			WidthLocked = true;
		}
		str = '<font color=' + color + '>' + str + '</font>';
		innerHTML += innerHTML ? "<br />\\n" + str : str;
		scrollTop += 14;
	}
}
</script>
HTML;
}

function tpl_auth($error){

$TEXT_DUMPER_ERROR = TEXT_DUMPER_ERROR;
$TEXT_DUMPER_BROWSER_ERROR = TEXT_DUMPER_BROWSER_ERROR;

$TEXT_DUMPER_LOGIN_HEADER = TEXT_DUMPER_LOGIN_HEADER;
$TEXT_DUMPER_LOGIN = TEXT_DUMPER_LOGIN;
$TEXT_DUMPER_PASSWORD = TEXT_DUMPER_PASSWORD;

$TEXT_DUMPER_FORBIDDEN = TEXT_DUMPER_FORBIDDEN;
$TEXT_DUMPER_DB_CONNECT = TEXT_DUMPER_DB_CONNECT;
$TEXT_DUMPER_DB_ERROR = TEXT_DUMPER_DB_ERROR;

return <<<HTML
<span id="error">
<fieldset>
<legend>$TEXT_DUMPER_ERROR</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr>
<td>$TEXT_DUMPER_BROWSER_ERROR</td>
</tr>
</table>
</fieldset>
</span>
<span id="body" style="display: none;">
{$error}
<fieldset>
<legend>$TEXT_DUMPER_LOGIN_HEADER</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr>
<td width="41%">$TEXT_DUMPER_LOGIN</td>
<td width="59%"><input name="login" type="text" class="text"></td>
</tr>
<tr>
<td>$TEXT_DUMPER_PASSWORD</td>
<td><input name="pass" type="password" class="text"></td>
</tr>
</table>
</fieldset>
</span>
<script>
document.getElementById('sjs').innerHTML = '+';
document.getElementById('body').style.display = '';
document.getElementById('error').style.display = 'none';
var jsEnabled = true;
</script>
HTML;
}

function tpl_l($str, $color = C_DEFAULT){

$TEXT_DUMPER_SUBMIT = TEXT_DUMPER_SUBMIT;
$TEXT_DUMPER_SEC = TEXT_DUMPER_SEC;
$TEXT_DUMPER_DIR_ERROR = TEXT_DUMPER_DIR_ERROR;
$TEXT_DUMPER_DOWNLOAD = TEXT_DUMPER_DOWNLOAD;
$TEXT_DUMPER_BACK = TEXT_DUMPER_BACK;
$TEXT_DUMPER_CREATE = TEXT_DUMPER_CREATE;
$TEXT_DUMPER_NAME_ERROR = TEXT_DUMPER_NAME_ERROR;
$TEXT_DUMPER_CONNECT = TEXT_DUMPER_CONNECT;
$TEXT_DUMPER_CONNECT_ERROR = TEXT_DUMPER_CONNECT_ERROR;
$TEXT_DUMPER_CREATE_FILE = TEXT_DUMPER_CREATE_FILE;
$TEXT_DUMPER_CHARSET_ERROR = TEXT_DUMPER_CHARSET_ERROR;
$TEXT_DUMPER_CHARSET = TEXT_DUMPER_CHARSET;
$TEXT_DUMPER_CHARSET_COLLATION = TEXT_DUMPER_CHARSET_COLLATION;
$TEXT_DUMPER_TABLE = TEXT_DUMPER_TABLE;
$TEXT_DUMPER_CONNECT1 = TEXT_DUMPER_CONNECT1;
$TEXT_DUMPER_PROCESS = TEXT_DUMPER_PROCESS;
$TEXT_DUMPER_MAKE = TEXT_DUMPER_MAKE;
$TEXT_DUMPER_MAKE1 = TEXT_DUMPER_MAKE1;
$TEXT_DUMPER_SIZE  = TEXT_DUMPER_SIZE;
$TEXT_DUMPER_MB  = TEXT_DUMPER_MB;
$TEXT_DUMPER_FILE_SIZE = TEXT_DUMPER_FILE_SIZE;
$TEXT_DUMPER_TABLES_COUNT = TEXT_DUMPER_TABLES_COUNT;
$TEXT_DUMPER_STRING_COUNT = TEXT_DUMPER_STRING_COUNT;
$TEXT_DUMPER_STRING_COUNT = TEXT_DUMPER_STRING_COUNT;
$TEXT_DUMPER_RESTORE = TEXT_DUMPER_RESTORE;
$TEXT_DUMPER_FILE_ERROR = TEXT_DUMPER_FILE_ERROR;
$TEXT_DUMPER_FILE_READ = TEXT_DUMPER_FILE_READ;
$TEXT_DUMPER_FILE_ERROR1 = TEXT_DUMPER_FILE_ERROR1;
$TEXT_DUMPER_QUERY_ERROR = TEXT_DUMPER_QUERY_ERROR;
$TEXT_DUMPER_RESTORED = TEXT_DUMPER_RESTORED;
$TEXT_DUMPER_DATE = TEXT_DUMPER_DATE;
$TEXT_DUMPER_QUERY_COUNT = TEXT_DUMPER_QUERY_COUNT;
$TEXT_DUMPER_TABLES_CREATED = TEXT_DUMPER_TABLES_CREATED;
$TEXT_DUMPER_STRINGS_CREATED = TEXT_DUMPER_STRINGS_CREATED;
$TEXT_DUMPER_MAX = TEXT_DUMPER_MAX;
$TEXT_DUMPER_MED = TEXT_DUMPER_MED;
$TEXT_DUMPER_MIN = TEXT_DUMPER_MIN;
$TEXT_DUMPER_NO = TEXT_DUMPER_NO;

$TEXT_DUMPER_BACKUP = TEXT_DUMPER_BACKUP;
$TEXT_DUMPER_DB = TEXT_DUMPER_DB;
$TEXT_DUMPER_FILTER = TEXT_DUMPER_FILTER;
$TEXT_DUMPER_COMPRESS = TEXT_DUMPER_COMPRESS;
$TEXT_DUMPER_COMPRESS_LEVEL = TEXT_DUMPER_COMPRESS_LEVEL;

$TEXT_DUMPER_RESTORE_DB = TEXT_DUMPER_RESTORE_DB;
$TEXT_DUMPER_FILE = TEXT_DUMPER_FILE;

$TEXT_DUMPER_TABLE_STATUS = TEXT_DUMPER_TABLE_STATUS;
$TEXT_DUMPER_TOTAL_STATUS = TEXT_DUMPER_TOTAL_STATUS;

$TEXT_DUMPER_ERROR = TEXT_DUMPER_ERROR;
$TEXT_DUMPER_BROWSER_ERROR = TEXT_DUMPER_BROWSER_ERROR;

$TEXT_DUMPER_LOGIN_HEADER = TEXT_DUMPER_LOGIN_HEADER;
$TEXT_DUMPER_LOGIN = TEXT_DUMPER_LOGIN;
$TEXT_DUMPER_PASSWORD = TEXT_DUMPER_PASSWORD;

$TEXT_DUMPER_FORBIDDEN = TEXT_DUMPER_FORBIDDEN;
$TEXT_DUMPER_DB_CONNECT = TEXT_DUMPER_DB_CONNECT;
$TEXT_DUMPER_DB_ERROR = TEXT_DUMPER_DB_ERROR;


$str = preg_replace("/\s{2}/", " &nbsp;", $str);
return <<<HTML
<script>l('{$str}', $color);</script>

HTML;
}

function tpl_enableBack(){
return <<<HTML
<script>document.getElementById('back').disabled = 0;</script>

HTML;
}

function tpl_s($st, $so){
$st = round($st * 100);
$st = $st > 100 ? 100 : $st;
$so = round($so * 100);
$so = $so > 100 ? 100 : $so;
return <<<HTML
<script>s({$st},{$so});</script>

HTML;
}

function tpl_backup_index(){

$TEXT_DUMPER_FORBIDDEN = TEXT_DUMPER_FORBIDDEN;

return <<<HTML
<center>
<h1>$TEXT_DUMPER_FORBIDDEN</h1>
</center>

HTML;
}

function tpl_error($error){

$TEXT_DUMPER_DB_CONNECT = TEXT_DUMPER_DB_CONNECT;

return <<<HTML
<fieldset>
<legend>$TEXT_DUMPER_DB_CONNECT</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr>
<td align="center">{$error}</td>
</tr>
</table>
</fieldset>

HTML;
}

function SXD_errorHandler($errno, $errmsg, $filename, $linenum, $vars) {

$TEXT_DUMPER_DB_ERROR = TEXT_DUMPER_DB_ERROR;

	if ($errno == 2048) return true;
	if (preg_match("/chmod\(\).*?: Operation not permitted/", $errmsg)) return true;
    $dt = date("Y.m.d H:i:s");
    $errmsg = addslashes($errmsg);

	echo tpl_l("{$dt}<br /><b>" . $TEXT_DUMPER_DB_ERROR . "</b>", C_ERROR);
	echo tpl_l("{$errmsg} ({$errno})", C_ERROR);
	echo tpl_enableBack();
	die();
}
?>
<?php $main->bottom(); ?>