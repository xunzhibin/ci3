<?php
namespace Xzb\Ci3\Http\Exception;

interface HttpExceptionInterface
{
	/**
	 * 获取 HTTP 状态码
	 * 
	 * @return int
	 */
	public function getHttpStatusCode(): int;
}
