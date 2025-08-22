<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;

class RegexValidator extends RuleValidator
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
		$this->checkParameterCount(1, $this->parameters, $this->getRuleName());

		return preg_match($this->parameters[0], $value) > 0;
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		return 'The :attribute format is invalid.';
	}

}
