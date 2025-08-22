<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;

class SameValidator extends RuleValidator
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

		if (! array_key_exists($this->parameters[0], $this->data)) {
			return false;
		}

		return $value === $this->data[$this->parameters[0]];
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		return 'The :attribute and :other must be consistent.';
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
			':other',
			$this->validator->getAttributeDisplayName($this->parameters[0]),
			parent::replaceMessagePlaceholders($message, $attribute)
		);
	}

}
