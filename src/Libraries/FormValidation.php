<?php
namespace Xzb\Ci3\Libraries;

use Xzb\Ci3\Validation\ValidatorInterface;
use Xzb\Ci3\Validation\MessageBag;

class FormValidation extends \CI_Form_validation implements ValidatorInterface
{
	/**
	 * 构造函数
	 * 
	 * @param array $rules
	 * @param array $data
	 * @return void
	 */
	public function __construct(array $rules = [], array $data = [])
	{
		parent::__construct([]);

		/**
		 * 设置 验证规则 时, 会检测 有没有 验证数据, 没有 验证数据时 无法设置 验证规则
		 * 验证数据 添加 默认值, 防止 设置 验证规则 失败
		 */
		$data = array_merge([
			'phpversion' => phpversion(),
		], $data);
		$this->reset_validation()->set_data($data)->set_rules($rules);
	}

	/**
	 * 是否 失败
	 * 
	 * @return bool
	 */
	public function fails()
	{
		$result = $this->run();

		return (bool)$this->error_array();
	}

	/**
	 * 错误消息包
	 * 
	 * @return \Illuminate\Support\MessageBag
	 */
	public function errors()
	{
		return new MessageBag($this->error_array());
	}

}
