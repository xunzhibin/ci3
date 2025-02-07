<?php

namespace Xzb\Ci3\Core\Exceptions\Http;

class InternalServerErrorException extends HttpException
{
	/**
	 * HTTP 状态码
	 * 
	 * @var int
	 */
	protected $httpStatusCode = 500;
}
