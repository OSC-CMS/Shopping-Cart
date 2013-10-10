<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiPayment extends CartET
{
	/**
	 * Возвращает установленные модули оплаты
	 */
	public function getInstalled($data = array())
	{
		$lang = (isset($data['lang'])) ? $data['lang'] : $_SESSION['language'];

		$payments = explode(';', MODULE_PAYMENT_INSTALLED);

		$aPayments = array();
		if (is_array($payments) && !empty($payments))
		{
			foreach ($payments as $payment)
			{
				$paymentName = str_replace('.php', '', $payment);
				$paymentFile = get_path('modules').'payment/'.$paymentName.'/'.$payment;
				$paymentLangFile = get_path('modules').'payment/'.$paymentName.'/'.$lang.'.php';
				
				if (is_file($paymentFile) && is_file($paymentLangFile))
				{
					require($paymentLangFile);
					$aPayments[$paymentName] = array(
						'id' => $paymentName,
						'text' => constant(MODULE_PAYMENT_.strtoupper($paymentName)._TEXT_TITLE)
					);
				}
			}
		}

		return $aPayments;
	}
}
?>