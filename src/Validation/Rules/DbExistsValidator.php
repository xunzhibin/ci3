<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;
use InvalidArgumentException;

class DbExistsValidator extends RuleValidator
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

		[$connection, $table] = str_contains($this->parameters[0], '.') ? explode('.', $this->parameters[0], 2) : [null, $this->parameters[0]];
		$filter = array_merge([
			$this->parameters[1] ?? $attribute => $value
		], $this->parseFilter(array_slice($this->parameters, 2)));

		$rows = load_class('Loader', 'core')
					->database((string)$connection, true)
					->from($table)
					->where($filter)
					->count_all_results();

		return $rows > 0;
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		// return 'The selected :attribute is invalid.';
		return 'The :attribute does not exist.';
	}

	/**
	 * 解析 过滤器
	 * 
	 * @param array $parameters
	 * @return array
	 * 
	 * @throws \InvalidArgumentException
	 */
	protected function parseFilter(array $parameters)
	{
		$filter = [];

		foreach ($parameters ?: [] as $column) {
			[$column, $value] = explode(':', $column, 2);

			if (str_starts_with($value, '$')) {
				if (! array_key_exists($field = ltrim($value, '$'), $this->data)) {
					throw new InvalidArgumentException('Validation rule ' . $this->getRuleName() . ' parameter is invalid.');
				}
				$value = $this->data[$field];
			}

			$filter[$column] = $value;
		}

		return $filter;
	}

}
