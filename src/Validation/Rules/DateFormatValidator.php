<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;
use DateTime;

class DateFormatValidator extends RuleValidator
{
	/**
	 * 验证
	 * 
	 * @param string $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function validate(string $attribute, $value): bool
	{
		$this->checkParameterCount(1, $this->parameters, 'date_format');

		foreach ($this->parameters as $format) {
			$date = DateTime::createFromFormat('!'.$format, $value);
			if ($date && $date->format($format) == $value) {
				return true;
			}
		}

		return false;
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		return 'The :attribute does not match the format :format.';
	}

	/**
	 * 替换 消息 占位符
	 * 
	 * @param string $message
	 * @param string $attribute
	 * @param string $rule
	 * @param array $parameters
	 * @return string
	 */
	public function replaceMessagePlaceholders(string $message, string $attribute): string
	{
		return str_replace(
			':format',
			$this->parameters[0],
			parent::replaceMessagePlaceholders($message, $attribute)
		);
	}

}
