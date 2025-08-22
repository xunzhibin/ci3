<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;
use Xzb\Ci3\Helpers\Date;
use InvalidArgumentException;

class DateComparisonValidator extends RuleValidator
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

		if (! is_string($value) && ! is_numeric($value) && ! $value instanceof DateTimeInterface) {
			return false;
		}

		return $this->compare($value, $this->getDependValue($this->parameters[1]), $this->parameters[0]);
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		$messages = [
			'>' => 'The :attribute must be a date after :date.',
			'>=' => 'The :attribute must be a date after or equal to :date.',
			'<' => 'The :attribute must be a date before :date.',
			'<=' => 'The :attribute must be a date before or equal to :date.',
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
		$message = parent::replaceMessagePlaceholders($message, $attribute);

		if (array_key_exists($this->parameters[1], $this->data)) {
			return str_replace(
				':date',
				$this->validator->getAttributeDisplayName($this->parameters[1]),
				$message
			);
		}

		return str_replace(
			':date',
			$this->validator->getValueDisplayName($attribute, $this->parameters[1]),
			$message
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

		$dependValue = $this->getDependValue($this->parameters[1]);

		if (! is_string($dependValue) && ! is_numeric($dependValue) && ! $dependValue instanceof DateTimeInterface) {
			throw new InvalidArgumentException('Validation rule ' . $this->getRuleName() . ' parameter format is invalid.');
		}

		if (! in_array($this->parameters[0], ['>', '<', '>=', '<='])) {
			throw new InvalidArgumentException('Validation rule ' . $this->getRuleName() . ' comparison operator is invalid.');
		}
	}

	/**
	 * 对比
	 * 
	 * @param mixed $first
	 * @param mixed $second
	 * @param string $operator
	 */
	protected function compare($first, $second, string $operator)
	{
		$firstDate = $first ? $this->parseDateTime($first) : null;
		$secondDate = $second ? $this->parseDateTime($second) : null;

		if (is_null($firstDate) || is_null($secondDate)) {
			return false;
		}

		$firstTimestamp = $firstDate->getTimestamp();
		$secondTimestamp = $secondDate->getTimestamp();

		switch ($operator) {
			// case '=':
			// 	return $firstTimestamp == $secondTimestamp;
			case '<':
				return $firstTimestamp < $secondTimestamp;
			case '>':
				return $firstTimestamp > $secondTimestamp;
			case '<=':
				return $firstTimestamp <= $secondTimestamp;
			case '>=':
				return $firstTimestamp >= $secondTimestamp;
			default:
				throw new InvalidArgumentException('Validation rule ' . $this->getRuleName() . ' comparison operator is invalid.');
		}
	}

	/**
	 * 解析 日期时间
	 * 
	 * @param string $value
	 * @return \DateTime|null
	 */
	protected function parseDateTime($value)
	{
		try {
			if (is_numeric($value)) {
				return Date::createFromTimestamp($value);
			}
				
			return @Date::parse($value) ?: null;
		}
		catch (\Exception $e) {

		}
	}

}
