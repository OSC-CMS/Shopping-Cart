<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/
/*
 WordPress, wordpress.org
*/
   function get_plugin_data( $plugin_file ) 
   { 
       //class plugin
       global $p;
	   
	    $fp = fopen($plugin_file, 'r');
	    $plugin_data = fread( $fp, 8192 );
	    fclose($fp);
		
	    preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $name );
	    preg_match( '|Plugin URI:(.*)$|mi', $plugin_data, $uri );
	    preg_match( '|Version:(.*)|i', $plugin_data, $version );
	    preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
	    preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
	    preg_match( '|Author URI:(.*)$|mi', $plugin_data, $author_uri );
	    preg_match( '|Plugin Group:(.*)$|mi', $plugin_data, $PluginGroup);
       
		
	    $plugin_data = array(
				'Name' => trim(isset($name[1])?$name[1]:''),
				'Title' => (!empty($name[1]) ? ($name[1]) : (ucfirst($p->name))), 
				'PluginURI' => trim(isset($uri[1])?$uri[1]:''), 
				'Description' => trim(isset($description[1])?$description[1]:''),
				'Author' => trim(isset($author_name[1])?$author_name[1]:''), 
				'AuthorURI' => trim(isset($author_uri[1])?$author_uri[1]:''), 
				'Version' => trim(isset($version[1])?$version[1]:''), 
				'PluginGroup' => trim(isset($PluginGroup[1])?$PluginGroup[1]:'')
				);
		
	    return $plugin_data;
   } 

?>