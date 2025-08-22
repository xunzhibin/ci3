<?php
namespace Xzb\Ci3\Validation\Contracts;

use Xzb\Ci3\Validation\Validator;
use InvalidArgumentException;
use Xzb\Ci3\Helpers\Str;

abstract class RuleValidator implements Rule
{
	/**
	 * 验证器 实例
	 * 
	 * @var Xzb\Ci3\Validation\Validator
	 */
	protected $validator;

	/**
	 * 验证 参数
	 * 
	 * @var array
	 */
	protected $parameters = [];

	/**
	 * 验证 数据
	 * 
	 * @var array
	 */
	protected $data = [];

	/**
	 * 规则 名
	 * 
	 * @var string
	 */
	protected $rule;

	/**
	 * 构造函数
	 * 
	 * @param array $parameters
	 * @return void
	 */
	public function __construct($parameters = [])
	{
		$this->parameters = is_array($parameters) ? $parameters : func_get_args();

		$this->rule = str_ends_with($className = Str::snakeCase(class_basename(static::class)), '_validator')
						? mb_substr($className, 0, -strlen('_validator'))
						: $className;
	}

	/**
	 * 设置 数据
	 * 
	 * @param array $data
	 * @return $this
	 */
	public function setData(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * 设置 验证器
	 * 
	 * @param \Xzb\Ci3\Validation\Validator
	 * @return $this
	 */
	public function setValidator(Validator $validator)
	{
		$this->validator = $validator;

		return $this;
	}

	/**
	 * 解析 规则 名
	 * 
	 * @return string
	 */
	public function getRuleName()
	{
		return $this->rule;
	}

	/**
	 * 获取 依赖值
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function getDependValue($key)
	{
		return $this->data[$key] ?? $key;
	}

	/**
	 * 检测 参数 个数
	 * 
	 * @param int $cout
	 * @param array $parameters
	 * @param string $rule
	 * @return void
	 * 
	 * @throws \InvalidArgumentException
	 */
	public function checkParameterCount(int $count, array $parameters, string $rule)
	{
		if (count($parameters) < $count) {
			throw new InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
		}
	}

	/**
	 * 替换 消息 占位符
	 * 
	 * @param string $message
	 * @param string $attribute
	 * @return string
	 */
	public function replaceMessagePlaceholders(string $message, string $attribute): string
	{
		$attributeDisplayName = $this->validator->getAttributeDisplayName($attribute);

		$message = str_replace(
			[':attribute', ':ATTRIBUTE', ':Attribute'],
			[$attributeDisplayName, strtoupper($attributeDisplayName), ucfirst($attributeDisplayName)],
			$message
		);

		return $message;
	}

}
