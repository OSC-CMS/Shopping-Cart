/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

src = SITE_WEB_DIR+"captcha.php";
function reload()
{
	document.captcha.src=SITE_WEB_DIR+'images/loading.gif';
	document.captcha.src=src+'?rand='+Math.random();
}