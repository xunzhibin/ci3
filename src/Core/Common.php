<?php

// ------------------------------------------------------------------------
if (! function_exists('_error_handler')) {
	/**
	 * 错误处理函数
	 * 
	 * @param int $errno
	 * @param string errmsg
	 * @param string errfile
	 * @param int $errline
	 * @return void
	 */
	function _error_handler(int $errno, string $errmsg, string $errfile, int $errline)
	{
		if (in_array($errno, [E_DEPRECATED, E_USER_DEPRECATED])) {
			return ;
		}

		load_class('Exceptions', 'core')->log_exception($errno, $errmsg, $errfile, $errline);

		load_class('Exceptions', 'core')->show_php_error($errno, $errmsg, $errfile, $errline);
	}
}

// ------------------------------------------------------------------------
if (! function_exists('_exception_handler')) {
	/**
	 * 异常处理函数
	 * 
	 * @param \Throwable $exception
	 * @return void
	 */
	function _exception_handler(\Throwable $exception)
	{
		load_class('Exceptions', 'core')->log_exception(
			'error',
			'Exception: ' . $exception->getMessage(),
			$exception->getFile(),
			$exception->getLine()
		);

		load_class('Exceptions', 'core')->show_exception($exception);
	}
}

// ------------------------------------------------------------------------
if (! function_exists('_shutdown_handler')) {
	/**
	 * 异常处理函数
	 * 
	 * @return void
	 */
	function _shutdown_handler()
	{
		if ( ! is_null($error = error_get_last())) {
			if (in_array($error['type'], [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE])) {
			// if ($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)) {
				_error_handler($error['type'], $error['message'], $error['file'], $error['line']);
			}
		}
	}
}
