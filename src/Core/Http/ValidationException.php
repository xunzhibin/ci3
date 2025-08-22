<?php
namespace Xzb\Ci3\Core\Http;

use Xzb\Ci3\Http\Exception\UnprocessableEntityException;
// use Xzb\Ci3\Http\Foundation\MessageBag;
use Xzb\Ci3\Validation\ValidatorInterface;

class ValidationException extends UnprocessableEntityException
{
	/**
	 * 验证器 实例
	 * 
	 * @var \Xzb\Ci3\Validation\ValidatorInterface
	 */
	public $validator;

	/**
	 * 设置 验证器 实例
	 * 
	 * @param \Xzb\Ci3\Validation\ValidatorInterface $validator
	 * @return $this
	 */
	public function setValidator(ValidatorInterface $validator)
	{
		$this->validator = $validator;

		return $this;
	}

	/**
	 * 获取 验证器 实例
	 * 
	 * @return \Xzb\Ci3\Validation\ValidatorInterface
	 */
	public function getValidator(): ValidatorInterface
	{
		return $this->validator;
	}

	// /**
	//  * 错误包 实例
	//  * 
	//  * @var \Xzb\Ci3\Http\Foundation\MessageBag
	//  */
	// protected $errorBag;

	// /**
	//  * 设置 错误包
	//  * 
	//  * @return $this
	//  */
	// public function setErrorBag(MessageBag $errorBag)
	// {
	// 	$this->errorBag = $errorBag;

	// 	return $this;
	// }

	// /**
	//  * 获取 错误包
	//  * 
	//  * @return \Xzb\Ci3\Http\Foundation\MessageBag
	//  */
	// public function getErrorBag()
	// {
	// 	return $this->errorBag;
	// }

}
