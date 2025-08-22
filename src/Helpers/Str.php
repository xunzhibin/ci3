<?php
namespace Xzb\Ci3\Helpers;

// 第三方 字符串库 https://www.doctrine-project.org/projects/inflector.html
use Doctrine\Inflector\InflectorFactory;
// "doctrine/inflector": "^2.0"

class Str
{
// ------------------------- 大驼峰命名法 -------------------------
	/**
	 * 大驼峰命名 缓存
	 *
	 * @var array
	 */
	protected static $upperCamelCache = [];

	/**
	 * 转为 大驼峰命名法
	 *
	 * 首字母大写
	 *
	 * @param string $value
	 * @return string
	 */
	public static function upperCamelCase(string $value): string
	{
		if (isset(static::$upperCamelCache[$value])) {
			return static::$upperCamelCache[$value];
		}

		return static::$upperCamelCache[$value] = InflectorFactory::create()
														->build()
														->classify($value);
	}

	/**
	 * 帕斯卡拼写法
	 * 
	 * @param string $value
	 * @return string
	 */
	public static function pascalCase(string $value): string
	{
		return static::upperCamelCase($value);
	}

// ------------------------- 小驼峰命名法 -------------------------
	/**
	 * 小驼峰命名 缓存
	 *
	 * @var array
	 */
	protected static $lowerCamelCache = [];

	/**
	 * 转为 小驼峰命名法
	 *
	 * 首字母小写
	 *
	 * @param string $value
	 * @return string
	 */
	public static function lowerCamelCase(string $value): string
	{
	    if (isset(static::$lowerCamelCache[$value])) {
	        return static::$lowerCamelCache[$value];
	    }

	    return static::$lowerCamelCache[$value] = InflectorFactory::create()
														->build()
														->camelize($value);
	}

	/**
	 * 驼峰命名法
	 * 
	 * @param string $value
	 * @return string
	 */
	public static function camelCase(string $value): string
	{
		return static::lowerCamelCase($value);
	}

// ------------------------- 蛇形命名法 -------------------------
	/**
	 * 蛇形命名 缓存
	 *
	 * @var array
	 */
	protected static $snakeCache = [];

	/**
	 * 转为 蛇形命名法
	 *
	 * @param  string  $value
	 * @param  string  $delimiter
	 * @return string
	 */
	public static function snakeCase(string $value, string $delimiter = '_'): string
	{
		if (isset(static::$snakeCache[$value][$delimiter])) {
			return static::$snakeCache[$value][$delimiter];
		}

		$newValue = InflectorFactory::create()->build()->tableize($value);

		if ($delimiter != $defaultDelimiter = '_') {
			$newValue = str_replace($defaultDelimiter, $delimiter, $newValue);
		}

		return static::$snakeCache[$value][$delimiter] = $newValue;
	}

// ------------------------- 单复数 -------------------------
	/**
	 * 转为 复数形式
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function pluralize(string $value): string
	{
		return InflectorFactory::create()->build()->pluralize($value);
	}

	/**
	 * 转为 单数形式
	 * 
	 * @param string $value
	 * @return string
	 */
	public static function singularize(string $value): String
	{
		return InflectorFactory::create()->build()->singularize($value);
	}

}
