<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

$os_action = array();
$os_action_tmp = array();
$os_rewrite_action = array();
$os_filter = array();

$os_action_plug = array();

function add_action($tag, $function_to_add, $priority = 10) 
{
	global $os_action;
	global $os_action_tmp;
	global $os_action_plug;

    global $p; //class plugins
		
	if (is_object($p))
	{
		 $p->info[ $p->name ][ $tag ][] = $function_to_add;
	}
	
	$os_action_plug [ $function_to_add ] = $p->name;
	
	$os_action[ $tag ][ $function_to_add ] = $priority;


	return true;
}

function osc_cms_eval($str)
{
    eval($str);
}

function add_button_send($value)
{
   return '<span class="button"><button type="submit" value="'.$value.'">'.$value.'</button></span>';
}

function add_button($tag, $name, $value = '')
{
	if (!empty($name) && !empty($tag))
	{
		switch ($tag)
		{
			case 'page':
				return '<a class="btn btn-xs btn-default" href="'.FILENAME_PLUGINS_PAGE.'?page='.$name.'">'.$value.'</a>';
				break;
			case 'main_page':
				return '<a class="btn btn-xs btn-default" href="'.FILENAME_PLUGINS_PAGE.'?main_page='.$name.'">'.$value.'</a>';
				break;
			case 'link':
				return '<a class="btn btn-xs btn-default" href="'.$name.'">'.$value.'</a>';
				break;
		}
	}
}


/*

*/
function add_style($path, &$head,  $group = '')
{
    $head[] =  array(
                  'link' => Array
                            (
                               'rel' => 'stylesheet',
                               'type' => 'text/css',
                               'href' => $path,
							   'group' => $group
                            )
				);
}

function add_js ($path, &$head, $group = '')
{
    if (!empty( $group ))
	{
        $head[] = Array ('js' => Array('src' => $path, 'group' => $group) );
	}	
    else
    {
        $head[] = Array ('js' => Array('src' => $path) );	
	}	
	
}

//добавления js кода в <head> 
function add_js_code ($code, &$head, $group = '')
{
    if (!empty( $group ))
	{
        $head[] = Array ('js' => Array('code' => $code, 
		                               'group' => $group) 
						);
	}	
    else
    {
        $head[] = Array ('js' => Array('code' => $code) );	
	}				
}

//добавления кода в <head> 
function add_head_code ($code, &$head, $group = '')
{
    if (!empty( $group ))
	{
        $head[] = Array ('other' => Array('code' => $code, 
		                               'group' => $group) 
						);
	}	
    else
    {
        $head[] = Array ('other' => Array('code' => $code) );	
	}				
}

//добавления  кода в <head>
function add_head_file ($file, &$head, $group = '')
{
	if ( is_file( $file ) )
    {
          ob_start();
		  
             require( $file );
			 
             $general_js = ob_get_contents();
			 
	         if (!empty($general_js)) 
			 {
			    add_head_code($general_js, $head, $group);
			 }
			
          ob_end_clean();
    }			
}

//добавление фильтра в плагинах
function add_filter($tag, $function, $priority = '10')
{
    global $os_filter;
    global $os_filter_name;
	global $p;
	
    $os_filter[ $tag ][ $priority ][] = $function;
	
	if ( count( $os_filter[ $tag ] ) >1 ) 	ksort($os_filter[ $tag ]);
    if ( count( $os_filter[ $tag ][ $priority ] ) >1 ) sort( $os_filter[ $tag ][ $priority ] );
	
	// func_name -> plug_name
	$os_filter_name[ $function ] = $p->name;
	
    if (is_object($p))
	{ 
		 $p->info[ $p->name ][ $tag ][] = $function;
	}
	
	return true;
}

/*
  удаляет js, с строчкой, в котором есть $src
*/
function remove_js ($src,  &$_meta_array)
{ 
   if (!empty($_meta_array))
   {
      foreach ($_meta_array as $_num => $_value)
	  {
	     if ( isset($_value['js']) && isset($_value['js']['src']) )
		 {
		       if (mb_strpos($_value['js']['src'], $src))
			   {
				  unset($_meta_array[$_num]['js']['src']);
			   }
		 }
	  }
   }
}

function is_head_js ($src,  &$_meta_array)
{ 
   if (!empty($_meta_array))
   {
      foreach ($_meta_array as $_num => $_value)
	  {
	     if ( isset($_value['js']) && isset($_value['js']['src']) )
		 {
		       if (mb_strpos($_value['js']['src'], $src))
			   {
				  return true;
			   }
		 }
	  }
	  
	  return false;
   }
   else
   {
      return false;
   }
}

/*
  удаляет метатегов определенной группы
*/
function remove_head_group ($group,  &$_meta_array)
{ 
   if (!empty($_meta_array))
   {
      foreach ($_meta_array as $_num => $_value)
	  {
          if ( isset($_value['js']) && isset($_value['js']['group']) && $_value['js']['group'] == $group)
		  {
		     unset($_meta_array[$_num]);
		  }
	
		  if ( isset($_value['link']) && isset($_value['link']['group']) && $_value['link']['group'] == $group )
		  {
		     unset($_meta_array[$_num]);
		  }
		  
		  
	  }
   }
}

/*
  удаляет css, с строчкой, в котором есть $src
*/
function remove_style ($src,  &$_meta_array)
{ 
   if (!empty($_meta_array))
   {
      foreach ($_meta_array as $_num => $_value)
	  {
	     if ( isset($_value['link']) && isset($_value['link']['href']) )
		 {
		       if (mb_strpos($_value['link']['href'], $src))
			   {
				  unset($_meta_array[$_num]['link']['href']);
			   }
		 }
	  }
   }
}

function apply_filters($tag, $value)
{
   return apply_filter ($tag, $value);
}

function apply_filter ($tag, $value)
{
   global $os_filter;
   global $p;
   global $os_filter_name;

   if ( isset($os_filter[ $tag ]) )
   {
        foreach ($os_filter[ $tag ] as $_filter_value)
	    {
	         foreach ($_filter_value as $_filter_func)
			 {
	              if (function_exists( $_filter_func ))
	              { 
		              $p->name  = $os_filter_name[ $_filter_func ];
			          $p->group = $p->info[ $p->name ]['group'];
		              $p->set_dir();

	                  $value = $_filter_func ($value);
					  
	              }
		     }
	  }
   }
   
   return $value;
}

function remove_action ($tag, $function_to_remove, $priority = 10)
{
	global $os_remove_action;
	
	$os_remove_action[$tag][$function_to_remove] = $priority; 
	
    return true;
}

function remove_action_array ()
{   
	global $os_remove_action;
	
	if (!empty($os_remove_action))
	{
        foreach ($os_remove_action as $tag => $functions_to_remove)
	    {
	         foreach ($functions_to_remove as $function_to_remove => $priority)
	         {
		          unset($os_remove_action[ $tag ][ $function_to_remove ]);  
	         }
	    }
    }
	else
    {  
	    //нет action. нечего удалять
	   return false;
	}
	
    return true;
}

//заменить один action на другой
function rewrite_action ($tag, $function, $function_to_add)
{   
    global $os_rewrite_action;
	
	$os_rewrite_action[ $tag ][ $function ] = $function_to_add;
	
	return true;
}

function plugurl()
{
   	global $p; //class plugins

    return http_path('catalog').$p->dir;
}

function plug_page( $name )
{	
    return http_path('catalog').'admin/plugins_page.php?page='.$name;
}

function plug_main_page( $name )
{	
    return http_path('catalog').'admin/plugins_page.php?main_page='.$name;
}

function plugdir()
{
   	global $p; //class plugins
	
   // $dir = _PLUG.dirname($p->name).'/';
	
	//$dir = str_replace('//', '/', $dir);
	//$dir = str_replace('\\\\', '\\', $dir);
	
	return get_path('catalog').$p->dir;
}

function do_action ($tag, $separator = '')
{
    global $os_action;
	global $os_action_plug;
	global $p;
	
	if (!empty($tag) && isset($os_action[$tag]))
	{
	    foreach ($os_action[$tag] as $_tag => $priority)
        {
              if (function_exists($_tag))
	          {
			        $p->name = $os_action_plug[$_tag];
					$p->group = $p->info[$p->name]['group'];
		            $p->set_dir();

	                $_tag();
			        echo $separator;
              }
        }
	}
}

//синоним функции do_action
function run_action ($tag, $separator = '')
{
    do_action ($tag, $separator);
}

function output_action($tag, $separator = '')
{
    global $os_action;
	global $os_action_plug;
	global $p;
	
	ob_start();
	
	if (!empty($tag) && isset($os_action[$tag]))
	{
	    foreach ($os_action[$tag] as $_tag => $priority)
        {
              if (function_exists($_tag))
	          {
			        $p->name = $os_action_plug[$_tag];
					
	                $_tag();
			        echo $separator;
              }
        }
	}
	
	$m_content = ob_get_contents();
	ob_end_clean();
	
	return $m_content;
}

function is_action($tags)
{
    global $os_action;
  
    if (isset($os_action[$tags]))
    {
        return true;
    }
    else
    {
        return false;
    }
}

if (!function_exists('_e'))
{
   function _e ($text)
   {
	  echo $text."\n";
	  return true;
   }
}

function add_item_menu($menu_title, $menu_link)
{
   global $add_item_menu;
   
   $add_item_menu[] = array('menu_title' => $menu_title, 
                            'menu_link' => $menu_link 
   );
}

function is_page( $name )
{
   global $PHP_SELF;
   
   $_php_self = $PHP_SELF;
   $_php_self = str_replace('.php', '', trim($_php_self) );
   $_php_self = str_replace(CATALOG.'admin/', '', $_php_self );
   $_php_self = str_replace(CATALOG, '', $_php_self );
   $_php_self = str_replace('/', '', $_php_self );

   if ($name == $_php_self) return true;
   else false;
}

function page_admin($func)
{
   return http_path('catalog').'admin/plugins_page.php?page='.$func;
}

function main_page_admin($func)
{
   return http_path('catalog').'admin/plugins_page.php?main_page='.$func;
}


?>