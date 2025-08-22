<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;

class RequiredValidator extends RuleValidator
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
		if (is_null($value)) {
			return false;
		}
		else if (is_countable($value)) {
			return count($value) > 0;
		}
		else if (is_string($value)) {
			return trim((string)$value) !== '';
		}
		// else if ($value instanceof File && ! $value->getPath()) {
		// 	return false;
		// else if ($value instanceof File) {
		// 	return ! $value->getPath();
		// 	return (bool)$value->getPath();
		// }

		return true;
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		return 'The :attribute field is required.';
	}

}
