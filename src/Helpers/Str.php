<?php

namespace Xzb\Ci3\Helpers;

// 第三方 字符串库 https://www.doctrine-project.org/projects/inflector.html
use Doctrine\Inflector\InflectorFactory;
// "doctrine/inflector": "^2.0"

class Str
{
	/**
	 * 蛇形命名 缓存
	 *
	 * @var array
	 */
	protected static $snakeCache = [];

	/**
	 * 大驼峰命名 缓存
	 *
	 * @var array
	 */
	protected static $upperCamelCache = [];

// 	/**
// 	 * 小驼峰命名 缓存
// 	 *
// 	 * @var array
// 	 */
// 	protected static $lowerCamelCache = [];

// ---------------------- 转换 ----------------------
	/**
	 * 转为 复数形式
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function plural(string $value): string
	{
		// 创建 新实例
		return InflectorFactory::create()->build()
								// 转 复数形式
								->pluralize($value);
	}

// 	/**
// 	 * 转为 单数形式
// 	 *
// 	 * @param  string  $value
// 	 * @return string
// 	 */
// 	public static function singular(string $value): string
// 	{
// 		// 创建 新实例
// 		return InflectorFactory::create()->build()
// 								// 转 单数形式
// 								->singularize($value);
// 	}

	/**
	 * 转为 蛇形命名法
	 *
	 * @param  string  $value
	 * @param  string  $delimiter
	 * @return string
	 */
	public static function snake(string $value, string $delimiter = '_'): string
	{
		$key = $value;

		// 已缓存
		if (isset(static::$snakeCache[$key][$delimiter])) {
			return static::$snakeCache[$key][$delimiter];
		}

		// 创建 新实例
		$value = InflectorFactory::create()->build()
								// 转 蛇形命名 字符串
								->tableize($value);

		// 检测 替换 分隔符
		if ($delimiter != $defaultDelimiter = '_') {
			$value = str_replace($defaultDelimiter, $delimiter, $value);
		}

		return static::$snakeCache[$key][$delimiter] = $value;
	}

	/**
	 * 转为 大驼峰命名法
	 *
	 * 首字母大写
	 *
	 * @param string $value
	 * @return string
	 */
	public static function upperCamel(string $value): string
	{
		$key = $value;

		// 已缓存
		if (isset(static::$upperCamelCache[$key])) {
			return static::$upperCamelCache[$key];
		}

		// 创建 新实例
		$value = InflectorFactory::create()->build()
								// 转 大驼峰命名法
								->classify($value);

		// 缓存 并 返回
		return static::$upperCamelCache[$key] = $value;
	}

// 	/**
// 	 * 转为 小驼峰命名法
// 	 *
// 	 * 首字母小写
// 	 *
// 	 * @param string $value
// 	 * @return string
// 	 */
// 	public static function lowerCamel(string $value): string
// 	{
// 	    $key = $value;

// 		// 已缓存
// 	    if (isset(static::$lowerCamelCache[$key])) {
// 	        return static::$lowerCamelCache[$key];
// 	    }

// 	    // 创建 新实例
// 	    $value = InflectorFactory::create()->build()
// 	                            // 转 小驼峰命名法
// 	                            ->camelize($value);

// 		// 缓存 并 返回
// 	    return static::$lowerCamelCache[$key] = $value;
// 	}

// 	/**
// 	 * 是否 以指定 子字符串 结尾
// 	 *
// 	 * @param string $string
// 	 * @param string|string[] $substrings
// 	 * @return bool
// 	 */
// 	public static function endsWith(string $string, $substrings): bool
// 	{
// 		foreach ((array)$substrings as $substring) {
// 			if (substr($string, -strlen($substring)) === (string) $substring) {
// 				return true;
// 			}
// 		}

// 		return false;
// 	}

// 	/**
// 	 * 替换 最后一个 指定 子字符串
// 	 *
// 	 * @param string $string
// 	 * @param string $search
// 	 * @param string $replace
// 	 * @return string
// 	 */
// 	public static function replaceLast(string $string, string $search, string $replace): string
// 	{
// 		if ($search) {
// 			$position = strrpos($string, $search);

// 			if ($position !== false) {
// 				return substr_replace($string, $replace, $position, strlen($search));
// 			}
// 		}

// 		return $string;
// 	}

// 	/**
// 	 * 是否 包含指定 子字符串
// 	 *
// 	 * @param string $string
// 	 * @param string|string[] $substrings
// 	 * @return bool
// 	 */
// 	public static function contains(string $string, $substrings): bool
// 	{
// 		foreach ((array)$substrings as $substring) {
// 			if ($substring !== '' && mb_strpos($string, $substring) !== false) {
// 				return true;
// 			}
// 		}

// 		return false;
// 	}

	/**
	 * 是否 以指定 子字符串 开头
	 *
	 * @param string $string
	 * @param string|string[] $substrings
	 * @return bool
	 */
	public static function startsWith(string $string, $substrings): bool
	{
		foreach ((array)$substrings as $substring) {
			if ($substring !== '' && substr($string, 0, strlen($substring)) === (string)$substring) {
				return true;
			}
		}

		return false;
	}

// 	/**
// 	 * 生成 字母、数字 随机字符串
// 	 *
// 	 * @param int $length
// 	 * @return bool
// 	 */
// 	public static function random(int $length): string
// 	{
//         $string = '';

//         while ($length > $strlen = strlen($string)) {
//             $size = $length - $strlen;

//             $bytes = random_bytes($size);

//             $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
//         }

//         return $string;
// 	}

	/**
	 * 是否为 JSON字符串
	 * 
	 * @param string $value
	 * @return bool
	 */
	public static function isJson($value): bool
	{
		if (! is_string($value)) {
			return false;
		}

		// try {
		// 	json_decode($value, true, 512, JSON_THROW_ON_ERROR);
		// }
		// catch (\JsonException $e) {
		// 	return false;
		// }

		// return true;

		json_decode($value, true);

		return (json_last_error() == JSON_ERROR_NONE);
	}

}
