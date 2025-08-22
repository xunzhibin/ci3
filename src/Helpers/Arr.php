<?php
namespace Xzb\Ci3\Helpers;

use ArrayAccess;

class Arr
{
	/**
	 * 是否 可作为数组访问
	 * 
	 * @param mixed $value
	 * @param bool
	 */
	public static function accessible($value): bool
	{
		return is_array($value) || $value instanceof ArrayAccess;
	}

	/**
	 * 是否 存在指定键
	 * 
	 * @param array|\ArrayAccess $array
	 * @param string|int $key
	 * @param bool
	 */
	public static function exists($array, $key): bool
	{
		if ($array instanceof ArrayAccess) {
			return $array->offsetExists($key);
		}

		if (is_float($key)) {
			$key = (string) $key;
		}

		return array_key_exists($key, $array);
	}

	/**
	 * 设置 值
	 * 
	 * 可以使用 '.' 符号
	 * 
	 * @param array $array
	 * @param string|int|null $key
	 * @param mixed $value
	 * @return array
	 */
	public static function set(array &$array, $key, $value)
	{
		if (is_null($key)) {
			return $array = $value;
		}

		$keys = explode('.', $key);
		foreach ($keys as $i => $key) {
			if (count($keys) === 1) {
				break;
			}

			unset($keys[$i]);

			if (! isset($array[$key]) || ! is_array($array[$key])) {
				$array[$key] = [];
			}

			$array = &$array[$key];
		}

		$array[array_shift($keys)] = $value;

		return $array;
	}

	/**
	 * 获取 值
	 * 
	 * 可以使用 '.' 符号
	 * 
	 * @param array|\ArrayAccess $array
	 * @param string|int|null $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($array, $key, $default = null)
	{
		if (! static::accessible($array)) {
			return value($default);
		}

		if (is_null($key)) {
			// return $array;
			return value($default);
		}

		if (static::exists($array, $key)) {
			return $array[$key];
		}

		if (! str_contains($key, '.')) {
			return $array[$key] ?? value($default);
		}

		foreach (explode('.', $key) as $segment) {
			if (static::accessible($array) && static::exists($array, $segment)) {
				$array = $array[$segment];
			}
			else {
				return value($default);
			}
		}

		return $array;
	}

	/**
	 * 是否存在指定项
	 * 
	 * @param array|\ArrayAccess $array
	 * @param string|array $keys
	 * @return bool
	 */
	public static function has($array, $keys): bool
	{
		$keys = (array)$keys;

		if (! $array || $keys === []) {
			return false;
		}

		foreach ($keys as $key) {
			$subkeyArray = $array;

			if (static::exists($array, $key)) {
				continue;
			}

			foreach (explode('.', $key) as $segment) {
				if (static::accessible($subkeyArray) && static::exists($subkeyArray, $segment)) {
					$subKeyArray = $subKeyArray[$segment];
				}
				else {
					return false;
				}
			}
		}

		return true;
	}

}
