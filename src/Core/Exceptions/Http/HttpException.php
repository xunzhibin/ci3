<?php

namespace Xzb\Ci3\Core\Exceptions\Http;

use \RuntimeException;
use Throwable;

class HttpException extends RuntimeException
{
	/**
	 * HTTP 状态码 短语
	 * 
	 * @var array
	 */
	protected $phrases = [
		400 => 'Bad Request',
		401 => 'Unauthorized',
		404 => 'Not Found',
		422 => 'Unprocessable Entity',
		500	=> 'Internal Server Error',
	];

	/**
	 * 第一个 异常类
	 * 
	 * @var \Throwable
	 */
	protected $firstException;

	/**
	 * HTTP 状态码
	 * 
	 * @var int
	 */
	protected $httpStatusCode;

	/**
	 * 错误消息
	 * 
	 * @var string
	 */
	protected $errorMessage;

	/**
	 * 构造函数
	 * 
	 * @param string $message
	 * @param int $code
	 * @param \Throwable $previous
	 * @return void
	 */
	public function __construct(string $message = null, int $code = null, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$this->firstException = $this->firstExceptionClass();
	}

	/**
	 * 获取 HTTP 状态码
	 * 
	 * @return int
	 */
	public function getHttpStatusCode() :int
	{
		return $this->httpStatusCode;
	}

	/**
	 * 设置 错误消息
	 * 
	 * @param string $message
	 * @return $this
	 */
	public function setErrorMessage(string $message)
	{
		$this->errorMessage = $message;

		return $this;
	}

	/**
	 * 获取 错误消息
	 * 
	 * @return string
	 */
	public function getErrorMessage(): string
	{
		return (string)$this->errorMessage;
	}

	/**
	 * 创建 HTTP响应对象
	 * 
	 * @return array
	 */
	public function toResponse()
	{
		$body = [
			'status'		=> false, // API 业务处理 状态
			'status_code'	=> $this->getHttpStatusCode(), // HTTP 状态码
			'errcode'		=> $this->getResponseErrCode(), // API 错误异常码
			'message'		=> $this->getResponseErrorMessage(), // API 错误描述
		];

		// debug 调试信息
		if (
			(defined('ENVIRONMENT') && in_array(ENVIRONMENT, ['development', 'testing']))
			|| ($_GET['jn_debug'] ?? false)
		) {
			// 回溯跟踪
			$body['debug']['backtrace'] = $this->getBacktraceDebug();
		}

		return $body;
	}

	/**
	 * 获取 响应错误异常码
	 * 
	 * @return string
	 */
	protected function getResponseErrCode(): string
	{
		$exceptionClassName = class_basename($this->firstException);

		return config_item('errcode')[$exceptionClassName] ?? $exceptionClassName;
	}

	/**
	 * 获取 响应错误信息
	 * 
	 * @return string
	 */
	protected function getResponseErrorMessage()
	{
		// return $this->phrases[$this->getHttpStatusCode()];
		return $this->getErrorMessage() ?: $this->phrases[$this->getHttpStatusCode()];
	}

	/**
	 * 回溯跟踪 debug
	 * 
	 * @return array
	 */
	protected function getBacktraceDebug(): array
	{
		$backtrace = [];

		$exception = $this->firstException;

		$backtrace[] = [
			'message'   => $exception->getMessage(), // 错误文言
			'file'      => $exception->getFile(), // 文件
			'line'      => $exception->getLine(), // 行号
		];
		foreach($exception->getTrace() as $trace) {
			unset($trace['args']);
			unset($trace['type']);
			$backtrace[] = $trace;
		}

		return $backtrace;
	}

	/**
	 * 第一个异常类
	 * 
	 * @return \Exception
	 */
	protected function firstExceptionClass()
	{
		$exception = $this;
		do {
			$isHasPrevious = false;
			if ($exception->getPrevious()) {
				$exception = $exception->getPrevious();
				$isHasPrevious = true;
			}

		} while($isHasPrevious);

		return $exception;
	}
}
