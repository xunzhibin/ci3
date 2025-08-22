<?php
namespace Xzb\Ci3\Core\Http;

use Xzb\Ci3\Http\Foundation\Request AS XzbRequest;
use Xzb\Ci3\Validation\ValidatorInterface;
use Xzb\Ci3\Libraries\FormValidation;
use Xzb\Ci3\Validation\Validator;
use Xzb\Ci3\Helpers\Arr;

class Request extends XzbRequest
{
	/**
	 * 验证器 实例
	 * 
	 * @var 
	 */
	protected $validator;

	/**
	 * 设置 验证器 实例
	 * 
	 * @param \Xzb\Ci3\Validation\Validator;
	 * @return $this
	 */
	public function setValidator(Validator $validator)
	{
		$this->validator = $validator;

		return $this;
	}

	/**
	 * 获取 验证器 实例
	 * 
	 * @return \Xzb\Ci3\Validation\Validator
	 */
	public function validator()
	{
		return $this->validator;
	}

	/**
	 * 验证 请求
	 * 
	 * @param array $rules
	 * @param array $customMessages
	 * @param array $customAttributes
	 * @return void
	 */
	public function validate(array $rules,  array $customMessages = [], array $customAttributes = [])
	{
		$this->validator = $this->getValidatorInstance($rules, $customMessages, $customAttributes);
		if ($this->validator->fails()) {
			$this->failedValidation($this->validator);
		}
	}

	/**
	 * 获取 验证器 实例
	 * 
	 * @param array $rules
	 * @param array $customMessages
	 * @param array $customAttributes
	 * @return \Xzb\Ci3\Validation\ValidatorInterface
	 */
	protected function getValidatorInstance(array $rules,  array $customMessages = [], array $customAttributes = [])
	{
		if ($this->validator) {
			return $this->validator;
		}
	
		$validator = new Validator($this->validationData(), $rules, $customMessages, $customAttributes);

		$this->setValidator($validator);

		return $this->validator;

		// $validator = new FormValidation(
		// 	$this->parseCi3ValidationRules($rules,  $customMessages, $customAttributes),
		// 	$this->validationData()
		// );
	}

	/**
	 * 解析 CI3 表单验证规则
	 * 
	 * @param array $rules
	 * @param array $customMessages
	 * @param array $customAttributes
	 * @return array
	 */
	protected function parseCi3ValidationRules(array $rules,  array $customMessages = [], array $customAttributes = [])
	{
		$errorMessages = [];
		foreach ($customMessages as $key => $message) {
			$keys = explode('.', $key);

			$attributeKey = reset($keys);
			$ruleKey = end($keys);
			if ($attributeKey && $ruleKey) {
				$errorMessages[$attributeKey][$ruleKey] = $message;
			}
		}

		$formRules = [];
		foreach ($rules as $attribute => $rule) {
			array_push($formRules, [
				'field' => $attribute,
				'label' => $customAttributes[$attribute] ?? 'lang:' . $attribute,
				'rules' => $rule,
				'errors' => $errorMessages[$attribute] ?? []
			]);
		}

		return $formRules;
	}

	/**
	 * 处理 验证失败
	 * 
	 * @param \Xzb\Ci3\Validation\ValidatorInterface $validator
	 * @return void
	 * 
	 * @throws \Xzb\Ci3\Core\Http\ValidationException
	 */
	protected function failedValidation(ValidatorInterface $validator)
	{
		throw (new ValidationException('The given data was invalid'))->setValidator($validator);
	}

	/**
	 * 验证 数据
	 * 
	 * @return array
	 */
	public function validationData(): array
	{
		return $this->all();
	}

	/**
	 * 合并
	 * 
	 * @param array $input
	 * @return $this
	 */
	public function merge(array $input)
	{
		$this->getInputSource()->add($input);

		return $this;
	}

	/**
	 * 替换
	 * 
	 * @param array $input
	 * @return $this
	 */
	public function replace(array $input)
	{
		$this->getInputSource()->replace($input);

		return $this;
	}

	/**
	 * 获取 已验证 数据
	 * 
	 * @param string|int|null $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getValidatedData($key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->validator->getValidatedData();
		}

		return Arr::get($this->validator->getValidatedData(), $key, $default);
	}

}
