<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;
use InvalidArgumentException;

class SizeComparisonValidator extends RuleValidator
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

		$comparedValue = $this->getDependValue($this->parameters[1]);

		switch ($this->parameters[0]) {
			// case '=':
			// 	return $value == $comparedValue;
			case '<':
				return $value < $comparedValue;
			case '>':
				return $value > $comparedValue;
			case '<=':
				return $value <= $comparedValue;
			case '>=':
				return $value >= $comparedValue;
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
			'>' => 'The :attribute must be greater than :value.',
			'>=' => 'The :attribute must be greater than or equal to :value.',
			'<' => 'The :attribute must be less than :value.',
			'<=' => 'The :attribute must be less than or equal to :value.',
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
			$this->validator->getAttributeDisplayName($this->parameters[1]),
			parent::replaceMessagePlaceholders($message, $attribute)
		);
	}

	/**
	 * 检测 参数
	 * 
	 * @return void
	 * 
	 * @throws \InvalidArgumentException
	 */
	public function checkParameter()
	{
		$this->checkParameterCount(2, $this->parameters, $this->getRuleName());

		// if (! in_array($this->parameters[0], ['>=', '<=', '='])) {
		// 	throw new InvalidArgumentException('Validation rule ' . $this->getRuleName() . ' comparison operator is invalid.');
		// }

		if (! is_numeric($this->getDependValue($this->parameters[1]))) {
			throw new InvalidArgumentException('Validation rule ' . $this->getRuleName() . ' parameter format is invalid.');
		}
	}

}
