<?php

namespace Xzb\Ci3\Helpers;

use RuntimeException;

class Transform
{
	/**
	 * 类型 缓存
	 * 
	 * @var array
	 */
	protected static $typeCache = [];

// -------------------------------------------- 数据类型 --------------------------------------------
	/**
	 * 解析 数据类型
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function resolveType(string $type): string
	{
		if (isset(static::$typeCache[$type])) {
			return static::$typeCache[$type];
		}

		// 自定义 日期时间 类型
		if (static::isCustomDateTimeType($type)) {
			$convertedType = 'custom_datetime';
		}
		else {
			$convertedType = trim(strtolower($type));
		}

		return static::$typeCache[$type] = $convertedType;
	}

	/**
	 * 是否为 自定义日期时间 类型
	 * 
	 * @return bool
	 */
	public static function isCustomDateTimeType(string $type): bool
	{
		return strncmp($type, $str = 'datetime:', strlen($str)) === 0;
	}

// -------------------------------------------- 整形 --------------------------------------------
	/**
	 * 转换为 整形
	 * 
	 * @param mixed $value
	 * @param int
	 */
	public static function toInteger($value): int
	{
		if (is_array($value)) {
			throw new RuntimeException('Array could not be converted to int');
		}

		return intval($value);
	}

// -------------------------------------------- 字符串 --------------------------------------------
	/**
	 * 转换为 字符串
	 * 
	 * @param mixed $value
	 * @param string
	 */
	public static function toString($value): string
	{
		return strval($value);
	}

// -------------------------------------------- 数组 --------------------------------------------
	/**
	 * 转换为 数组
	 * 
	 * @param mixed $value
	 * @return array
	 */
	public static function toArray($value): array
	{
		if (is_string($value)) {
			$result = json_decode($value, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				if (is_array($result)) {
					return $result;
				}
				$value = $result;
			}

			if (! strlen($value)) {
				return [];
			}
		}

		return (array)$value;
	}

// -------------------------------------------- 布尔 --------------------------------------------
	/**
	 * 转换为 布尔类型
	 * 
	 * @param mixed $value
	 * @param bool
	 */
	public static function toBool($value): bool
	{
		return boolval($value);
	}

// -------------------------------------------- JSON --------------------------------------------
	/**
	 * 转换为 JSON 字符串
	 * 
	 * @param mixed $value
	 * @return string
	 */
	public static function toJson($value): string
	{
		if (Str::isJson($value)) {
			return $value;
		}

		$json = json_encode($value);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new RuntimeException('Unable to encode to JSON: ' . json_last_error_msg());
		}

		return $json;
	}

// -------------------------------------------- JSON --------------------------------------------
	/**
	 * 转换为 对象
	 * 
	 * @param mixed $value
	 * @return object
	 */
	public static function toObject($value): object
	{
		if (is_string($value)) {
			$result = json_decode($value);
			if (json_last_error() === JSON_ERROR_NONE) {
				if (is_object($result)) {
					return $result;
				}
				$value = $result;
			}

			if (! strlen($value)) {
				return new stdClass();
			}
		}

		return (object)$value;
	}

// -------------------------------------------- 日期时间 --------------------------------------------
	/**
	 * 转换为 日期时间 自定义格式
	 * 
	 * @param mixed $value
	 * @param string $format
	 * @return mixed
	 */
	public static function toDateTimeCustomFormat($value, string $format)
	{
		if (empty($value)) {
			return $value;
		}

		return Date::parse($value)->format($format);
	}

	/**
	 * 转换为 Unix时间戳
	 * 
	 * @param mixed $value
	 * @param int
	 */
	public static function toTimestamp($value): int
	{
		if (is_numeric($value)) {
			return intval($value);
		}

		return static::toDateTimeCustomFormat($value, $format = 'U');
	}

	/**
	 * 转换为 标准日期格式
	 * 
	 * @param mixed $value
	 * @return string
	 */
	public static function toDateFormat($value): string
	{
		if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
			return $value;
		}

		return static::toDateTimeCustomFormat($value, $format = 'Y-m-d');
	}

	/**
	 * 转换为 标准时间格式
	 * 
	 * @param mixed $value
	 * @return string
	 */
	public static function toTimeFormat($value): string
	{
		if (preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $value)) {
			return $value;
		}

		return static::toDateTimeCustomFormat($value, $format = 'H:i:s');
	}

	/**
	 * 转换为 标准日期时间格式
	 * 
	 * @param mixed $value
	 * @return string
	 */
	public static function toDateTimeFormat($value): string
	{
		if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $value)) {
			return $value;
		}

		return static::toDateTimeCustomFormat($value, $format = 'Y-m-d H:i:s');
	}


// -------------------------------------------- 转换数据类型 --------------------------------------------
	/**
	 * 转换 数据类型
	 * 
	 * @param string $type
	 * @param mixed $value
	 * @return mixed
	 */
	public static function dataType(string $type, $value)
	{
		switch (static::resolveType($type)) {
			// 布尔类型
			case 'bool':
			case 'boolean':
				return static::toBool($value);
			// 整形
			case 'int':
			case 'integer':
				return static::toInteger($value);
			// 字符串
			case 'string':
			case 'string':
				return static::toString($value);
			// 数组
			case 'array':
				return static::toArray($value);
			// 对象
			case 'object':
				return static::toObject($value);
			// JSON 字符串
			case 'json':
				return static::toJson($value);
			// Unix时间戳
			case 'timestamp':
				return static::toTimestamp($value);
			// 日期
			case 'date':
				return static::toDateFormat($value);
			// 日期时间
			case 'datetime':
				return static::toDateTimeFormat($value);
			// 自定义 日期时间
			case 'custom_datetime':
				$format = explode(':', $type, 2)[1];
				return static::toDateTimeCustomFormat($value, $format);
		}

		return $value;
	}

	/**
	 * 转换为 数据库 数据类型
	 * 
	 * @param string $type
	 * @param mixed $value
	 * @return mixed
	 */
	public static function DBDataType(string $type, $value)
	{
		switch(strtoupper($type)) {
			// 整形
			case 'TINYINT':
			case 'SMALLINT':
			case 'MEDIUMINT':
			case 'INT':
			case 'INTEGER':
			case 'BIGINT':
				return static::toInteger($value);
			// 非二进制 字符串
			case 'CHAR':
			case 'VARCHAR':
			case 'TINYTEXT':
			case 'TEXT':
			case 'MEDIUMTEXT':
			case 'LONGTEXT':
				return static::toString($value);
			case 'JSON':
				return static::toJson($value);
			// 日期 时间戳
			case 'DATE':
				return static::toDateFormat($value);
			case 'TIME':
				return static::toTimeFormat($value);
			// case 'YEAR':
			// 	return static::toDateTimeCustomFormat($value, $format = 'Y');
			case 'DATETIME':
			case 'TIMESTAMP':
				return static::toDateTimeFormat($value);
		}

		return $value;
	}

}
