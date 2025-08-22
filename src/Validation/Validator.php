<?php
namespace Xzb\Ci3\Validation;

use Xzb\Ci3\Helpers\Str;
use Xzb\Ci3\Helpers\Arr;
use Xzb\Ci3\Http\Foundation\MessageBag;
use stdClass;

class Validator implements ValidatorInterface
{
	/**
	 * 验证 数据
	 * 
	 * @var array
	 */
	protected $data;

	/**
	 * 验证 规则
	 * 
	 * @var array
	 */
	protected $rules;

	/**
	 * 验证 规则 名称
	 * 
	 * @var array
	 */
	protected $ruleNames;

	/**
	 * 自定义 错误消息
	 * 
	 * @var array
	 */
	protected $customMessages = [];

	/**
	 * 自定义 属性名称
	 * 
	 * @var array
	 */
	protected $customAttributes = [];

	/**
	 * 自定义 值 名称
	 * 
	 * @var array
	 */
	protected $customValues = [];

	/**
	 * 错误消息包 实例
	 * 
	 * @var object
	 */
	protected $messages;

	/**
	 * DB 查询 结果集
	 * 
	 * @var array
	 */
	protected $queryResults = [];

	/**
	 * 一条规则失败, 停止之后的所有验证
	 * 
	 * @var bool
	 */
	protected $stopOnFirstFailure = false;

	/**
	 * 构造函数
	 * 
	 * @param array $data
	 * @param array $rules
	 * @param array $customMessages
	 * @param array $customAttributes
	 * @param array $customValues
	 * @return void
	 */
	public function __construct(array $data, array $rules, array $customMessages = [], array $customAttributes = [], array $customValues = [])
	{
		$this->data = $data;
		// $this->rules = $rules;
		$this->customMessages = $customMessages;
		$this->customAttributes = $customAttributes;
		$this->customValues = $customValues;

		$this->setRules($rules);
	}


	/**
	 * 失败
	 * 
	 * @return bool
	 */
	public function fails()
	{
		return ! $this->passes();
	}

	/**
	 * 通过
	 * 
	 * @return bool
	 */
	public function passes(): bool
	{
		$this->messages = new MessageBag;

		foreach ($this->rules as $attribute => $rules) {
			if ($this->stopOnFirstFailure && $this->messages->isNotEmpty()) {
				break;
			}

			foreach ($rules as $ruleInstance) {
				if ($this->shouldStopValidating($attribute)) {
					break;
				}

				if (! $ruleInstance->validate($attribute, $this->getValue($attribute))) {
					$this->messages->set($attribute, $ruleInstance->replaceMessagePlaceholders(
						$this->getCustomMessage($attribute, $ruleInstance->getRuleName()) ?: $ruleInstance->message(),
						$attribute
					));
				}
			}
		}

		return $this->messages->isEmpty();
	}

	/**
	 * 是否 停止验证
	 * 
	 * @param string $attribute
	 * @return bool
	 */
	protected function shouldStopValidating(string $attribute): bool
	{
		if (in_array('if_exist', $this->ruleNames[$attribute] ?? [])) {
			return ! array_key_exists($attribute, $this->data);
		}
		if (in_array('bail', $this->ruleNames[$attribute] ?? [])) {
			return $this->messages->has($attribute);
		}
	
		return false;
	}

	/**
	 * 设置 验证 规则
	 * 
	 * @param array $rules
	 * @return $this
	 */
	public function setRules(array $rules)
	{
		$ruleNames = [];

		foreach ($rules as $attribute => $attributeRules) {
			$names = [];
			$rules[$attribute] = array_map(function ($rule) use (&$names) {
				// return $this->parseRuleInstance($rule);
				$ruleInstance = $this->parseRuleInstance($rule);

				array_push($names, $ruleInstance->getRuleName());

				return $ruleInstance;
			}, $this->explodeRules($attributeRules));
			$ruleNames[$attribute] = $names;
		}

		$this->rules = $rules;
		$this->ruleNames = $ruleNames;

		return $this;
	}

	// /**
	//  * 获取 验证 规则
	//  * 
	//  * @param string $attribute
	//  * @return array
	//  */
	// protected function getRule(string $attribute): array
	// {

	// }

	/**
	 * 分割 规则
	 * 
	 * @param array|string $rules
	 * @return array
	 */
	protected function explodeRules($rules): array
	{
		if (is_string($rules)) {
			return explode('|', $rules);
		}

		return $rules;
	}

	/**
	 * 解析 规则 实例
	 * 
	 * @param string $rule
	 * @param \Xzb\Ci3\Validation\Contracts\Rule
	 */
	protected function parseRuleInstance($rule): Contracts\Rule
	{
		if (is_string($rule)) {
			$parameters = [];
			if (str_contains($rule, ':')) {
				[$rule, $parameter] = explode(':', $rule, 2);
		
				$parameters = str_getcsv($parameter);
			}
	
			$ruleClass = Rules::class . '\\' . Str::upperCamelCase(trim($rule)) . 'Validator';
			$rule = new $ruleClass($parameters);
		}

		return $rule->setValidator($this)->setData($this->data);
	}

	/**
	 * 获取 属性值
	 * 
	 * @param string $attribute
	 * @return mixed
	 */
	protected function getValue(string $attribute)
	{
		return Arr::get($this->data, $attribute);
	}

	/**
	 * 获取 验证 数据
	 * 
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * 获取 错误消息包 实例
	 * 
	 * @return \Xzb\Ci3\Validation\MessageBag
	 */
	public function messages(): MessageBag
	{
		if (! $this->messages) {
			$this->passes();
		}

		return $this->messages;
	}

	/**
	 * 获取 错误
	 * 
	 * @return \Xzb\Ci3\Validation\MessageBag
	 */
	public function errors()
	{
		return $this->messages();
	}

	/**
	 * 获取 自定义 错误消息
	 * 
	 * @param string $attribute
	 * @param string $rule
	 * @return string string
	 */
	public function getCustomMessage(string $attribute, string $rule): ?string
	{
		foreach (["{$attribute}.{$rule}", $rule, $attribute] as $key) {
			if (array_key_exists($key, $this->customMessages)) {
				$message = $this->customMessages[$key];
				if ($key === $attribute && is_array($message) && array_key_exists($rule, $message)) {
					return $message[$rule];
				}

				return $message;
			}
		}

		return null;
	}

	/**
	 * 获取 属性 显示名称
	 * 
	 * @param string $attribute
	 * @return string
	 */
	public function getAttributeDisplayName(string $attribute): string
	{
		return $this->customAttributes[$attribute] ?? str_replace('_', ' ', Str::snakeCase($attribute));
	}

	/**
	 * 获取 值 显示名词
	 * 
	 * @param string $attribute
	 * @param mixed $value
	 * @return string
	 */
	public function getValueDisplayName(string $attribute, $value): string
	{
		if (isset($this->customValues[$attribute][$value])) {
			return $this->customValues[$attribute][$value];
		}

		return (string)$value;
	}

	/**
	 * 设置 DB 查询 结果
	 * 
	 * @param string $attribute
	 * @param string $value
	 * @param array $info
	 * @return $this
	 */
	public function setQueryResult(string $attribute, string $value, ?array $result)
	{
		$this->queryResults[$attribute][$value] = $result;

		return $this;
	}

	/**
	 * 获取 DB 查询 结果
	 * 
	 * @param string $attribute
	 * @return array
	 */
	public function getQueryResults(string $attribute = null, string $value = null): ?array
	{
		if (is_null($attribute)) {
			return $this->queryResults;
		}

		if (is_null($value)) {
			return $this->queryResults[$attribute] ?? null;
		}

		return $this->queryResults[$attribute][$value] ?? null;
	}

	/**
	 * 获取 已验证 数据
	 * 
	 * @return array
	 */
	public function getValidatedData(): array
	{
		$missingValue = new stdClass;

		$results = [];
		foreach ($this->rules as $attribute => $rules) {
			$value = Arr::get($this->getData(), $attribute, $missingValue);

			if ($value !== $missingValue) {
				Arr::set($results, $attribute, $value);
			}
		}

		return $results;
	}

}
