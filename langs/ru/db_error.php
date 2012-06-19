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

define('DB_ERR_MAIL', 'Администратор </dev/null>'); // Укажите E-Mail адрес и имя получателя, куда будут приходить письма с технической информацией, в случае возникновения проблем с MySQL сервером.

if (is_file(_CATALOG.'db_error.php'))
{
   $db_err_msg = file_get_contents(_CATALOG.'db_error.php');
}

define('DB_ERR_MSG', '<!-----db_error-----></table></table></table></div>'.$db_err_msg.'<!-----/db_error----->'); // Сообщение, которое будет выводиться при возникновении проблем с MySQL сервером.

define('MYSQL QUERY ERROR_TEXT', 'Проблемы с MySQL');
define('MYSQL QUERY ERROR_SUBJECT', 'Проблемы с MySQL сервером!');
define('MYSQL QUERY ERROR_SERVER_NAME', 'Сервер: ');
define('MYSQL QUERY ERROR_REMOTE_ADDR', 'Адрес: ');
define('MYSQL QUERY ERROR_REFERER', 'Реферер: ');
define('MYSQL QUERY ERROR_REQUESTED', 'Страница: ');
define('MYSQL QUERY ERROR_FROM', 'От: db_error@');

?>