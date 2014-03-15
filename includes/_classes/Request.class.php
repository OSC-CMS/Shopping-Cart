<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiRequest extends CartET
{
	private $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->data = $_REQUEST;
	}

	/**
	 * Определение метода запроса
	 */
	public function method($method = '')
	{
		if (!empty($method))
			return strtolower($_SERVER['REQUEST_METHOD']) == strtolower($method);

		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Проверяет ajax запрос
	 */
	public function isAjax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false;
	}

	/**
	 * Проверяет существование параметра в запросе
	 */
	public function has($param)
	{
		return isset($this->data[$param]);
	}

	/**
	 * Возвращает значение параметра запроса
	 */
	public function get($param, $default = false)
	{
		if (!$this->has($param)) return $default;

		return $this->data[$param];
	}

	/**
	 * Устанавливает параметр и значение
	 */
	public function set($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * Возвращает все параметры
	 */
	public function getAll()
	{
		return $this->data;
	}
}