<?php

// ------------------------------------------------------------------------
if (! function_exists('class_basename')) {
	/**
	 * 获取 对象或类的 basename
	 * 
	 * @param string|object $class
	 * @return string
	 */
	function class_basename($class)
	{
		$class = is_object($class) ? get_class($class) : $class;

		return basename(str_replace('\\', '/', $class));
	}
}

// ------------------------------------------------------------------------
if (! function_exists('value')) {
	/**
	 * 默认值
	 * 
	 * @param mixed $value
	 * @param mixed ...$args
	 * @return mixed
	 */
	function value($value, ...$args)
	{
		return $value instanceof \Closure ? $value(...$args) : $value;
	}
}
