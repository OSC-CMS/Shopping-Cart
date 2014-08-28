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

class order_total 
{
	var $modules;

	function credit_selection() 
	{
		$selection_string = '';
		$credit_class_string = '';
		if (MODULE_ORDER_TOTAL_INSTALLED) 
        {
			$header_string = '<div class="checkout-paymen-gift-title">'.TABLE_HEADING_CREDIT.'</div>'."\n";
			$header_string .= '<table border="0" width="100%" cellspacing="0" cellpadding="0">'."\n";

			reset($this->modules);
			$output_string = '';
			while (list (, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if ($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class) {
					$use_credit_string = $GLOBALS[$class]->use_credit_amount();
					if ($selection_string == '')
						$selection_string = $GLOBALS[$class]->credit_selection();
					if (($use_credit_string != '') || ($selection_string != '')) {
						$output_string .= '<tr>'."\n".'<td><b>'.$GLOBALS[$class]->header.'</b></td>'.$use_credit_string;
						$output_string .= '</tr>'."\n";
						$output_string .= $selection_string;
					}

				}
			}
			if ($output_string != '') {
				$output_string = $header_string.$output_string;
				$output_string .= '</table>';
			}
		}
		return $output_string;
	}

	function update_credit_account($i) {
		if (MODULE_ORDER_TOTAL_INSTALLED) {
			reset($this->modules);
			while (list (, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if (($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class)) {
					$GLOBALS[$class]->update_credit_account($i);
				}
			}
		}
	}

	function collect_posts() {

		if (MODULE_ORDER_TOTAL_INSTALLED) {
			reset($this->modules);
			while (list (, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if (($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class)) {
					$post_var = 'c'.$GLOBALS[$class]->code;
					if ($_POST[$post_var]) {
						$_SESSION[$post_var] = $_POST[$post_var];
					}
					$GLOBALS[$class]->collect_posts();
				}
			}
		}
	}

	function pre_confirmation_check() {
		global $order;
		if (MODULE_ORDER_TOTAL_INSTALLED) {
			$total_deductions = 0;
			reset($this->modules);
			$order_total = $order->info['total'];
			while (list (, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				$order_total = $this->get_order_total_main($class, $order_total);
				if (($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class)) {
					$total_deductions = $total_deductions + $GLOBALS[$class]->pre_confirmation_check($order_total);
					$order_total = $order_total - $GLOBALS[$class]->pre_confirmation_check($order_total);
				}
			}
			if ($order->info['total'] - $total_deductions <= 0) {
				$_SESSION['credit_covers'] = true;
			} else { 
				unset ($_SESSION['credit_covers']);
			}
		}
	}

	function apply_credit() {
		if (MODULE_ORDER_TOTAL_INSTALLED) {
			reset($this->modules);
			while (list (, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if (($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class)) {
					$GLOBALS[$class]->apply_credit();
				}
			}
		}
	}

	function clear_posts() {

		if (MODULE_ORDER_TOTAL_INSTALLED) {
			reset($this->modules);
			while (list (, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if (($GLOBALS[$class]->enabled && $GLOBALS[$class]->credit_class)) {
					$post_var = 'c'.$GLOBALS[$class]->code;
					unset ($_SESSION[$post_var]);
				}
			}
		}
	}

	function get_order_total_main($class, $order_total) {
		global $credit, $order;
		return $order_total;
	}

	function order_total() {
		if (defined('MODULE_ORDER_TOTAL_INSTALLED') && os_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
			$this->modules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);
			$modules = $this->modules;
			sort($modules); 
			reset($modules);
			while (list (, $value) = each($modules)) 
			{
				include (DIR_FS_DOCUMENT_ROOT.'modules/order_total/'.substr($value, 0, strrpos($value, '.')).'/'.$_SESSION['language'].'.php');
				include (DIR_FS_DOCUMENT_ROOT.'modules/order_total/'.substr($value, 0, strrpos($value, '.')).'/'.$value);
			

			    $class = substr($value, 0, strrpos($value, '.'));
				$GLOBALS[$class] = new $class ();
			}
			unset($modules);
		}
	}

	function process() {
		$order_total_array = array ();
		if (is_array($this->modules)) {
			reset($this->modules);
			while (list (, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if ($GLOBALS[$class]->enabled) {
					$GLOBALS[$class]->process();

					for ($i = 0, $n = sizeof($GLOBALS[$class]->output); $i < $n; $i ++) {
						if (os_not_null($GLOBALS[$class]->output[$i]['title']) && os_not_null($GLOBALS[$class]->output[$i]['text'])) {
							$order_total_array[] = array (
							
							'code' => $GLOBALS[$class]->code,
							'title' => $GLOBALS[$class]->output[$i]['title'],
							'text' => $GLOBALS[$class]->output[$i]['text'].' '.$_SESSION['currencySymbol'],
							'value' => $GLOBALS[$class]->output[$i]['value'],
							'sort_order' => $GLOBALS[$class]->sort_order
							);
						}
					}
				}
			}
		}

		return $order_total_array;
	}

	function output() {
		$outputArray = array();
		if (is_array($this->modules)) {
			reset($this->modules);
			while (list (, $value) = each($this->modules)) {
				$class = substr($value, 0, strrpos($value, '.'));
				if ($GLOBALS[$class]->enabled) {
					$size = sizeof($GLOBALS[$class]->output);
					for ($i = 0; $i < $size; $i ++)
					{
						$outputArray[] = array(
							'title' => $GLOBALS[$class]->output[$i]['title'],
							'text' => $GLOBALS[$class]->output[$i]['text'],
						);
					}
				}
			}
		}

		return $outputArray;
	}
}
?>
