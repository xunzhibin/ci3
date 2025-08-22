<?php
namespace Xzb\Ci3\Core\Http;

class FormRequest extends Request
{
	/**
	 * 验证
	 * 
	 * @return void
	 */
	public function validateResolved()
	{
		$this->beforeValidation();

		$this->validate($this->rules(), $this->messages(), $this->attributes());

		// $instance = $this->getValidatorInstance();
		// if ($instance->fails()) {
		// 	$this->failedValidation($instance);
		// }

		$this->afterValidation();
	}

	/**
	 * 验证 之前
	 * 
	 * @return void
	 */
	protected function beforeValidation()
	{

	}

	/**
	 * 验证 之后
	 * 
	 * @return void
	 */
	protected function afterValidation()
	{

	}

	/**
	 * 验证 规则
	 * 
	 * @return array
	 */
	public function rules(): array
	{
		return [];
	}

	/**
	 * 自定义 错误消息
	 * 
	 * @return array
	 */
	public function messages(): array
	{
		return [];
	}

	/**
	 * 自定义 错误属性
	 * 
	 * @return array
	 */
	public function attributes()
	{
		return [];
	}

}
