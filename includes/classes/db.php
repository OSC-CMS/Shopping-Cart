<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

    $db = new db();

    class db {
        var $link;
        var $server = DB_SERVER;
        var $username = DB_SERVER_USERNAME;
        var $password = DB_SERVER_PASSWORD;
        var $database = DB_DATABASE;
        var $use_pconnect = USE_PCONNECT;
        var $cache;
        /* constant */
        var $DB_CACHE = false;
        var $STORE_DB_TRANSACTIONS = false;
        var $DISPLAY_DB_QUERY = false;
        var $SEARCH_ENGINE_FRIENDLY_URLS = false;
        var $AJAX_CART = false;

        function connect() 
        {
            if ($this->use_pconnect == 'true') 
            {
                $this->link = mysql_pconnect($this->server, $this->username, $this->password);
            } 
            else 
            {
                $this->link = @mysql_connect($this->server, $this->username, $this->password, $this->link); 
            }

            if ($this->link)
            {
                @mysql_select_db($this->database);
                @mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
            }

            if (!$this->link) 
            {
                $db->error("connect", mysql_errno(), mysql_error());
            }

            return true;
        }

        function query($query) 
        {
            global $query_counts;
            global $db_query;
            $query_counts++; 

            if (STORE_DB_TRANSACTIONS == 'true') 
            {
                error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
            }

            $mtime = microtime(); 
            $mtime = explode(" ",$mtime); 
            $mtime = $mtime[1] + $mtime[0]; 
            $tstart = $mtime; 

            $result = mysql_query($query) or $this->error($query, mysql_errno(), mysql_error());

            $mtime = microtime(); 
            $mtime = explode(" ",$mtime); 
            $mtime = $mtime[1] + $mtime[0]; 
            $tend = $mtime; 
            $tpassed = ($tend - $tstart); 

            $tpassed = number_format($tpassed, 5);

            if (STORE_DB_TRANSACTIONS == 'true') 
            {
                $result_error = mysql_error();
                error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
            }

            if (!$result) 
            {
                $this->error($query, mysql_errno(), mysql_error());
            }

            if (DISPLAY_DB_QUERY == 'true')
            {

                $db_text = $query;
                $db_text = str_replace("\r\n", "", $db_text); 
                $db_text = str_replace("\n","",$db_text);
                $db_text = strtolower($db_text);
                $db_text = trim($db_text);
                $db_text = preg_replace("|[\s]+|s", " ", $db_text);

                //$db_query[$query_counts] = $db_text;

                if (isset($db_query[$db_text]))
                {
                    $db_query[ $db_text ]['num']++;
                    $db_query[ $db_text ]['time'] = $tpassed;
                }	
                else
                {
                    $db_query[ $db_text ]['num'] = 1;
                    $db_query[ $db_text ]['time'] = $tpassed;
                }		
            }

            return $result;
        }

        function fetch_array(&$db_query,$cq=false)
        {
            if ($this->DB_CACHE=='true' && $cq) 
            {
                if (!count($db_query)) return false;
                if (is_array($db_query)) 
                {
                    $curr = current($db_query);
                    next($db_query);
                }
                return $curr;
            } 
            else 
            {
                if (is_array($db_query)) 
                {
                    $curr = current($db_query);
                    next($db_query);
                    return $curr;
                }
                return @mysql_fetch_array($db_query, MYSQL_ASSOC);
            }
        }

        function error($query, $errno, $error) 
        {



            $log = date("d/m/Y H:m:s",time()) . ' | ' . $errno . ' - ' . $error . ' | ' . $query . ' | ' . $_SERVER["REQUEST_URI"] . "\n";

            echo '<textarea class="round" style="color:red;width:100%; height:100px;">'.$log.'</textarea>';
            die();
        }

        function db_query($query) 
        {
            if (DB_CACHE == 'true') 
            {
                $result = $this->cached($query);
            } 
            else 
            {
                $result = $this->query($query);
            }
            return $result;
        }


        function cached($query)
        {
            global $query_counts;
            global $db_query;
            $query_counts++; 

            if (isset($db_query[$query]))
            {
                $db_query[$query]['num']++;
                $db_query[$query]['time'] = $tpassed;
            }	
            else
            {
                $db_query[$query]['num'] = 1;
                $db_query[$query]['time'] = $tpassed;
            }	
            // get HASH ID for filename
            $id=md5($query);

            // cache File Name
            $file=SQL_CACHEDIR.$id.'.db';

            // file life time
            $expire = DB_CACHE_EXPIRE; // 24 hours

            if (STORE_DB_TRANSACTIONS == 'true') {
                error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
            }

            if (file_exists($file) && filemtime($file) > (time() - $expire)) {

                // get cached resulst
                $result = unserialize(implode('',file($file)));

            } else {

                if (file_exists($file)) @unlink($file);

                // get result from DB and create new file
                $result = $this->query($query, $this->link) or $this->error($query, mysql_errno(), mysql_error());

                if (STORE_DB_TRANSACTIONS == 'true') {
                    $result_error = mysql_error();
                    error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
                }

                // fetch data into array
                while ($record = $this->fetch_array($result))
                    $records[]=$record;


                // safe result into file.

                $stream = serialize($records);
                $fp = fopen($file,"w");
                fwrite($fp, $stream);
                $this->cache[$id] = $stream;
                fclose($fp);
                $result = unserialize(implode('',file($file)));

            }

            return $result;
        }

        function input($string) 
        {
            if (function_exists('mysql_real_escape_string')) 
            {
                return mysql_real_escape_string($string);
            } 
            elseif (function_exists('mysql_escape_string')) 
            {
                return mysql_escape_string($string);
            }

            return addslashes($string);
        }

        function prepare_input($string) 
        {
            if (is_string($string)) 
            {
                return trim(stripslashes($string));
            } 
            elseif (is_array($string)) 
            {
                reset($string);
                while (list($key, $value) = each($string)) 
                {
                    $string[$key] = $this->prepare_input($value);
                }
                return $string;
            } 
            else 
            {
                return $string;
            }
        }

        function data_seek($db_query, $row_number,$cq=false) 
        {
            if (DB_CACHE=='true' && $cq) 
            {
                if (!count($db_query)) return;
                return $db_query[$row_number];
            } 
            else 
            {
                if (!is_array($db_query)) return mysql_data_seek($db_query, $row_number);
            }
        }

        function categoriesstatus_for_product($product_id) 
        {
            $categorie_query = "SELECT categories_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES."WHERE products_id='".$product_id."'";
            $categorie_query = $this->db_query($categorie_query);

            while ($categorie_data = $this->fetch_array($categorie_query, true)) 
            {
                if (os_check_categories_status($categorie_data['categories_id']) >= 1) 
                {
                    return 1;
                } 
                else 
                {
                    return 0;
                }
                echo $categorie_data['categories_id'];
            }
        } 

        function close() 
        {
            return mysql_close($this->link);
        }

        function insert_id() 
        {
            return mysql_insert_id();
        }

        function output($string) 
        {
            return htmlspecialchars($string);
        }

        function select_db($database) 
        {
            return mysql_select_db($database);
        }

        function fetch_fields($db_query) 
        {
            return mysql_fetch_field($db_query);
        }

        function num_rows($db_query,$cq=false) 
        {
            if (DB_CACHE=='true' && $cq) 
            {
                if (!count($db_query)) return false;
                return count($db_query);
            } 
            else 
            {
                if (!is_array($db_query)) return mysql_num_rows($db_query);
            }
        }

        //получает количество рядов, задействованных в предыдущей MySQL-операции.
        function affected_rows()
        {
            return mysql_affected_rows ();
        }

        /* function insert_array($table, $data)
        {
        $insert_count = 40;
        $c = 0;
        print_r($data);
        if (count($data) > 0)
        {
        $cols_array = array();

        foreach ($data as $value)
        {
        foreach ($value as $name=>$val)
        {
        $_val[] = $val;
        $cols_array[$name]=1;
        }

        $_val_insert = '('.implode(',', $_val ).')';
        $_val_array[] = $_val_insert;
        $_val_insert = '';
        $_val = '';

        $c ++;

        if ($c >= $insert_count)
        {
        $_val_array = implode(',', $_val_array );

        //print_r($_val_array);

        print_r($cols_array);
        //$_val_array = '';
        die();
        //$this->query('insert into '.$table.' values');
        }
        }

        }


        }
        */
        function perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link') 
        {
            reset($data);

            if ($action == 'insert') 
            {
                $query = 'insert into ' . $table . ' (';
                while (list($columns, ) = each($data)) 
                {
                    $query .= $columns . ', ';
                }

                $query = substr($query, 0, -2) . ') values (';
                reset($data);

                while (list(, $value) = each($data)) 
                {
                    $value = (is_Float($value)) ? sprintf("%.F",$value) : (string)($value);
                    switch ($value) 
                    {
                        case 'now()':
                            $query .= 'now(), ';
                            break;
                        case 'null':
                            $query .= 'null, ';
                            break;
                        default:
                            $query .= '\'' . $this->input($value) . '\', ';
                            break;
                    }
                }
                $query = substr($query, 0, -2) . ')';
            } 
            elseif ($action == 'update') 
            {
                $query = 'update ' . $table . ' set ';
                while (list($columns, $value) = each($data)) 
                {
                    $value = (is_Float($value)) ? sprintf("%.F",$value) : (string)($value);
                    switch ($value) 
                    {
                        case 'now()':
                            $query .= $columns . ' = now(), ';
                            break;
                        case 'null':
                            $query .= $columns .= ' = null, ';
                            break;
                        default:
                            $query .= $columns . ' = \'' . $this->input($value) . '\', ';
                            break;
                    }
                }
                $query = substr($query, 0, -2) . ' where ' . $parameters;
            }

            return $this->query($query, $link);
        }

    }
?>