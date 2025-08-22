<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;
use InvalidArgumentException;


class LengthComparisonValidator extends RuleValidator
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
		$this->checkParameter();

		$length = is_countable($value) ? count($value) : mb_strlen((string)$value);

		switch ($this->parameters[0]) {
			case '=':
				return $length == $this->parameters[1];
			// case '<':
			// 	return $length < $this->parameters[1];
			// case '>':
			// 	return $length > $this->parameters[1];
			case '<=':
				return $this->parameters[0] <= $length;
			case '>=':
				return $this->parameters[0] >= $length;
			default:
				throw new InvalidArgumentException('Validation rule ' . $this->getRuleName() . ' comparison operator is invalid.');
		}
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		$messages = [
			'=' => 'The :attribute must be :value characters.',
			'>=' => 'The :attribute must not be greater than :value characters.',
			'<=' => 'The :attribute must be at least :value characters.',
		];

		return $messages[$this->parameters[0]] ?? '';
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
			':value',
			$this->parameters[1],
			parent::replaceMessagePlaceholders($message, $attribute)
		);
	}

	/**
	 * 检测 参数
	 * 
	 * @return void
	 * 
	 * @throws \TypeError
	 */
	public function checkParameter()
	{
		$this->checkParameterCount(2, $this->parameters, $this->getRuleName());

		if (! in_array($this->parameters[0], ['>=', '<=', '='])) {
			throw new InvalidArgumentException('Validation rule ' . $this->getRuleName() . ' comparison operator is invalid.');
		}

		if (! preg_match($pattern = '/^\d+$/', $this->parameters[1])) {
			throw new InvalidArgumentException('Validation rule ' . $this->getRuleName() . ' parameter format is invalid.');
		}
	}

}
