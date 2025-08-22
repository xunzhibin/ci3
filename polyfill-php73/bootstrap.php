<?php

if (\PHP_VERSION_ID >= 70300) {
	return;
}

// ------------------------------------------------------------------------
if (! function_exists('is_countable')) {
	/**
	 * 是否是可计数的值
	 * 
	 * @param mixed $var
	 * @return bool
	 */
	function is_countable($var): bool
	{
		return is_array($var) || $var instanceof \Countable;
	}
}
