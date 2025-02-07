<?php

namespace Xzb\Ci3\Core\Exceptions\Http;

class NotFoundException extends HttpException
{
	/**
	 * HTTP 状态码
	 * 
	 * @var int
	 */
	protected $httpStatusCode = 404;
}
