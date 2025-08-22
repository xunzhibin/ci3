<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;

class IfExistValidator extends RuleValidator
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
		return true;
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		return '';
	}

}
