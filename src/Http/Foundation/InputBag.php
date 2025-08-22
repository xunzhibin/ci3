<?php
namespace Xzb\Ci3\Http\Foundation;

use InvalidArgumentException;
use UnexpectedValueException;

final class InputBag extends ParameterBag
{
	/**
	 * 获取
	 * 
	 * @param string $key
	 * @param string|int|float|bool|null|array $default
	 * @return string|int|float|bool|null|array
	 */
	public function get(string $key, $default = null)
	{
		if (! $this->isDataTypeForInput($default)) {
			throw new InvalidArgumentException(
				sprintf('Expected a scalar value as a 2nd argument to "%s()", "%s" given.', __METHOD__, gettype($default))
			);
		}

		$value = parent::get($key, $default);

		if (! $this->isDataTypeForInput($value)) {
			throw new UnexpectedValueException(sprintf('Input value "%s" contains a non-scalar value.', $key));
		}

		return $value;
	}

	/**
	 * 设置
	 * 
	 * @param string $Key
	 * @param string|int|float|bool|array|null $value
	 * @return void
	 */
	public function set(string $key, $value)
	{
		if (! $this->isDataTypeForInput($value)) {
			throw new InvalidArgumentException(sprintf('Expected a scalar, or an array as a 2nd argument to "%s()", "%s" given.', __METHOD__, gettype($value)));
		}

		$this->parameters[$key] = $value;
	}

	/**
	 * 是否为 输入数据类型
	 * 
	 * @param mixed $value
	 * @return bool
	 */
	public function isDataTypeForInput($value): bool
	{
		if (is_null($value) || is_array($value) || is_scalar($value)) {
			return true;
		}

		return false;
	}

}
