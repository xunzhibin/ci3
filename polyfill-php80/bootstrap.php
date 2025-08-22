<?php

if (\PHP_VERSION_ID >= 80000) {
	return;
}

// ------------------------------------------------------------------------
if (! function_exists('str_contains')) {
	/**
	 * 是否包含指定子串
	 * 
	 * @param string $haystack
	 * @param string $needle
	 * @return string
	 */
	function str_contains(string $haystack, string $needle): bool
	{
		// return stripos($haystack, $needle) !== false;
		return mb_stripos($haystack, $needle) !== false;
		// return strpos($haystack, $needle) !== false;
		// return '' === $needle || false !== strpos($haystack, $needle);
		// return mb_strpos($haystack, $needle) !== false;
		// return $needle !== '' && mb_strpos($haystack, $needle) !== false;
	}
}

// ------------------------------------------------------------------------
if (! function_exists('str_starts_with')) {
	/**
	 * 是否以指定子串开头
	 * 
	 * @param mixed $var
	 * @return bool
	 */
	function str_starts_with(string $haystack, string $needle): bool
	{
        // return mb_substr($haystack, 0, mb_strlen($needle)) === $needle;
		// return 0 === strncmp($haystack, $needle, \strlen($needle));
		return 0 === strncmp($haystack, $needle, mb_strlen($needle));
	}
}
