<?php
namespace Xzb\Ci3\Validation\Contracts;

use Xzb\Ci3\Validation\Validator;

interface Rule
{
	/**
	 * 验证
	 * 
	 * @param string $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function validate(string $attribute, $value): bool;

	/**
	 * 错误消息
	 * 
	 * @return string
	 */
	public function message();

	/**
	 * 设置 验证数据
	 * 
	 * @param array $data
	 * @return $this
	 */
	public function setData(array $data);

	/**
	 * 设置 验证器
	 * 
	 * @param \Xzb\Ci3\Validation\Validator $validator
	 * @return $this
	 */
	public function setValidator(Validator $validator);

}
