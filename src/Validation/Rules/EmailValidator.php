<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;

class EmailValidator extends RuleValidator
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
		$option = strtolower($parameters[0] ?? null) === 'unicode'
					? FILTER_FLAG_EMAIL_UNICODE
					: 0;

		return filter_var((string)$value, FILTER_VALIDATE_EMAIL, $option) !== false;
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		return 'The :attribute must be a valid email address.';
	}

}
