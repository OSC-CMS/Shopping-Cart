<?php
/*
	Plugin Name: Редактор ЧПУ
	Plugin URI: http://osc-cms.com/extend/plugins
	Version: 1.0
	Description: Плагин реализует возможность редактировать, генерировать ЧПУ ссылки всех типов данных
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: SEO
*/

global $p;

if($p->info['seo_url_editor']['status'] == 1)
{
	define('SEO_URL_EDITOR_VERSION', '1.0');
}

add_action('page_admin', 'seo_url_editor_page');
add_action('head_admin', 'seo_url_editor_admin_head');
add_action('admin_menu', 'seo_url_editor_menu');

function seo_url_editor_page()
{
	function seo_url_editor_admin_head()
	{
		_e('<link rel="stylesheet" type="text/css" href="'.plugurl().'admin/css/admin_style.css">');
		_e('<script type="text/javascript" src="'.plugurl().'admin/js/tipsy.js"></script>');
		_e('<script type="text/javascript" src="'.plugurl().'admin/js/admin_js.js"></script>');
	}
	require_once (dirname(__FILE__).'/seo_url_editor_func.php');
	include (dirname(__FILE__).'/seo_url_editor_page.php');
}

function seo_url_editor_menu()
{
	add_plug_menu('Редактор ЧПУ', 'plugins_page.php?page=seo_url_editor_page');
}

function seo_url_editor_install()
{
	add_option('seo_url_editor_button', '', 'readonly');
}

function seo_url_editor_button_readonly()
{
	return add_button('page', 'seo_url_editor_page', 'Управление');
}

function seo_url_editor_remove()
{}
?>