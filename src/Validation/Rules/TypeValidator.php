<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;
use InvalidArgumentException;

class TypeValidator extends RuleValidator
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

		switch (strtolower($this->normalizeType($this->parameters[0]))) {
			case 'integer':
				return filter_var($value, FILTER_VALIDATE_INT) !== false;
			case 'string':
				return is_string($value);
			case 'boolean':
				$acceptable = [true, false, 0, 1, '0', '1'];
				return in_array($value, $acceptable, true);
			case 'array':
				return is_array($value);

			case 'natural':
				return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0,] ]) !== false;
				// return ctype_digit($value);
			case 'natural_no_zero':
				return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1,] ]) !== false;
				// return ctype_digit($value) && (string)$value !== '0';
			case 'numeric':
				return is_numeric($value);

			case 'alpha';
				return is_string($value) && preg_match('/\A[a-zA-Z]+\z/u', $value);
			case 'alpha_numeric':
				return (is_string($value) || is_numeric($value)) && preg_match('/\A[a-zA-Z0-9]+\z/u', $value);
			case 'alpha_dash':
				return (is_string($value) || is_numeric($value)) && preg_match('/\A[a-zA-Z0-9_-]+\z/u', $value);

			default:
				throw new InvalidArgumentException('Validation rule ' . $this->getRuleName() . ' parameters is invalid.');
		}
        // 'float' => 'is_float',
        // 'double' => 'is_float',
        // 'real' => 'is_float',
        // 'scalar' => 'is_scalar',
        // 'iterable' => 'is_iterable',
        // 'countable' => 'is_countable',
        // 'callable' => 'is_callable',
        // 'object' => 'is_object',
        // 'resource' => 'is_resource',
        // 'null' => 'is_null',
        // 'cntrl' => 'ctype_cntrl',
        // 'digit' => 'ctype_digit',
        // 'graph' => 'ctype_graph',
        // 'lower' => 'ctype_lower',
        // 'print' => 'ctype_print',
        // 'punct' => 'ctype_punct',
        // 'space' => 'ctype_space',
        // 'upper' => 'ctype_upper',
        // 'xdigit' => 'ctype_xdigit',
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		$messages = [
			'integer' => 'The :attribute must be an integer.',
			'string' => 'The :attribute must be a string.',
			'boolean' => 'The :attribute field must be true or false.',
			'array' => 'The :attribute must be an array.',

			'natural' => 'The :attribute must only contain digits.',
			'natural_no_zero' => 'The :attribute must must only contain digits and must be greater than zero.',
			'numeric' => 'The :attribute must be a number.',

			'alpha' => 'The :attribute must only contain letters.',
			'alpha_num' => 'The :attribute must only contain letters and numbers.',
			'alpha_numeric' => 'The :attribute must only contain letters and numbers.',
			'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
		];

		// This value should be of type :value
		return $messages[strtolower($this->normalizeType($this->parameters[0]))] ?? '';
	}

	/**
	 * 规范化 类型
	 * 
	 * @param string $type
	 * @return string
	 */
	protected function normalizeType(?string $type)
	{
		$aliases = [
			'int' => 'integer',
			'long' => 'integer',
			'bool' => 'boolean',
			'alpha_num' => 'alpha_numeric',
		];

		return $aliases[strtolower($this->parameters[0])] ?? $type;
	}

}
