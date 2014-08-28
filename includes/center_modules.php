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

$module = '';
if (VIS_MAIN_NEW == 'true')
{
	require(_MODULES.FILENAME_NEW_PRODUCTS);
}

if (VIS_MAIN_FEATURES == 'true')
{
	require(_MODULES.FILENAME_FEATURED);
}

if (VIS_MAIN_NEWS == 'true')
{
	require(_MODULES.FILENAME_NEWS);
}

if (VIS_MAIN_UPCOMING == 'true')
{
	require(_MODULES.FILENAME_UPCOMING_PRODUCTS);
}

return $module;
?>