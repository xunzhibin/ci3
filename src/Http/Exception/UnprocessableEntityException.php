<?php
namespace Xzb\Ci3\Http\Exception;

class UnprocessableEntityException extends HttpException
{
	/**
	 * 构造函数
	 * 
	 * @param string $message
	 * @param int $code
	 * @param \Throwable $previous
	 */
	public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
	{
		parent::__construct(422, $message, $code, $previous);
	}

}
