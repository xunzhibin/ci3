<?php

namespace Xzb\Ci3\Core;

use Xzb\Ci3\Core\Exceptions\ValidationException;
use Xzb\Ci3\Helpers\Traits\Sort;

class Request
{
	use Sort;

	/**
	 * CI 输入类
	 * 
	 * @var object
	 */
	public $input;

	/**
	 * 构造函数
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->input =& load_class('Input', 'core');

		$this->autoVerify();
	}

	/**
	 * 请求 参数
	 * 
	 * @return array
	 */
	public function param(): array
	{
		return $this->input->all();
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
	 * 验证
	 * 
	 * @param array $data
	 * @param array $rules
	 * @return void
	 * 
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function validate(array $data, array $rules)
	{
		$validation = load_class('form_validation');

		$validation->reset_validation()
					->set_data($data)
					->set_rules($rules)
					->run();

		if (count($errors = $validation->error_array())) {
			throw (new ValidationException('Request parameter exception'))->setMessages($errors);
		}
	}

	/**
	 * 自动验证
	 * 
	 * @return void
	 */
	public function autoVerify()
	{
		if ($rules = $this->rules()) {
			$this->validate($this->input->all(), $rules);
		}
	}

// ------------------------------- 魔术方法 -------------------------------
	/**
	 * 动态 获取 属性
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->input->input($key);
	}

}
