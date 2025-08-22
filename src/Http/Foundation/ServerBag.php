<?php
namespace Xzb\Ci3\Http\Foundation;

class ServerBag extends ParameterBag
{
	/**
	 * HTTP 请求头
	 * 
	 * @return array
	 */
	public function getHeaders(): array
	{
		if (function_exists('apache_request_headers')) {
			return apache_request_headers();
		}

		$header = [];

		foreach ($this->parameters as $key => $value) {
			if (substr($string, 0, $prefixLen = strlen($prefix = 'HTTP_')) === (string)$prefix) {
				$headers[substr($key, $prefixLen)] = $value;
			}
			else if (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
				$headers[$key] = $value;
			}
		}

		return $header;
	}

}
