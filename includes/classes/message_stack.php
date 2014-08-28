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

class messageStack
{
	var $size = 0;

	function messageStack()
	{
		$this->messages = array();

		if (isset($_SESSION['messageToStack']))
		{
			$messageToStack = $_SESSION['messageToStack'];

			for ($i=0, $n=sizeof($messageToStack); $i<$n; $i++)
			{
				$this->add($messageToStack[$i]['class'], $messageToStack[$i]['text'], $messageToStack[$i]['type']);
			}

			unset($_SESSION['messageToStack']);
		}
	}

	function add($class, $message, $type = 'error')
	{
		$this->messages[] = array('class' => $class, 'text' => $message, 'type' => $type);
		$this->size++;
	}

	function add_session($class, $message, $type = 'error')
	{
		if (!isset($_SESSION['messageToStack']))
		{
			$_SESSION['messageToStack'] = array();
		}

		$_SESSION['messageToStack'][] = array('class' => $class, 'text' => $message, 'type' => $type);
	}

	function output()
	{
		$tpl = new osTemplate;

		$tpl->assign('aMessages', $this->messages);

		$tpl->caching = 0;
		$templateFile = $tpl->fetch(CURRENT_TEMPLATE.'/module/messages.html');

		return $templateFile;
	}

	function reset()
	{
		$this->messages = array();
		$this->size = 0;
	}
}
?>