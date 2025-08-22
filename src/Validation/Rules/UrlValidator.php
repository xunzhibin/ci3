<?php
namespace Xzb\Ci3\Validation\Rules;

use Xzb\Ci3\Validation\Contracts\RuleValidator;

class UrlValidator extends RuleValidator
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
		if ($this->parameters) {
			$scheme = strtolower((string)parse_url((string)$value, PHP_URL_SCHEME));
			if (! in_array($scheme, array_map('strtolower', $this->parameters), true)) {
				return false;
			}
		}

		return filter_var((string)$value, FILTER_VALIDATE_URL) !== false;
	}

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message(): string
	{
		return 'The :attribute must be a valid URL.';
	}

}
