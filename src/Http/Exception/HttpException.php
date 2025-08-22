<?php
namespace Xzb\Ci3\Http\Exception;

use \Exception;
use \RuntimeException;
use Xzb\Ci3\Http\Exception\HttpExceptionInterFace;

// class HttpException extends Exception implements HttpExceptionInterFace
class HttpException extends RuntimeException implements HttpExceptionInterFace
{
	/**
	 * HTTP 状态码
	 * 
	 * @var int
	 */
	private $httpStatusCode;

	/**
	 * 构造函数
	 * 
	 * @param int $httpStatusCode
	 * @param string $message
	 * @param int $code
	 * @param \Throwable $previous
	 */
	public function __construct(int $httpStatusCode, string $message = '', int $code = 0, \Throwable $previous = null)
	{
		$this->httpStatusCode = $httpStatusCode;

		parent::__construct($message, $code, $previous);
	}

	/**
	 * 获取 HTTP 状态码
	 * 
	 * @return int
	 */
	public function getHttpStatusCode(): int
	{
		return $this->httpStatusCode;
	}

}
