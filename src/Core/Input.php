<?php

namespace Xzb\Ci3\Core;

class Input extends \CI_Input
{


// ------------------------------- 重构 -------------------------------
	// /**
	//  * 获取 php://input 输入项
	//  * 
	//  * @param string $index
	//  * @param bool $xss_clean
	//  * @return mixed
	//  */
	// public function input_stream($index = NULL, $xss_clean = NULL)
	// {
	// 	if ( ! is_array($this->_input_stream)) {
	// 		// 按 json 解析 为 数组
	// 		$this->_input_stream = json_decode($this->raw_input_stream, true);

	// 		// 按 json 解析失败
	// 		if (
	// 			json_last_error() !== JSON_ERROR_NONE
	// 			|| $this->_input_stream === null
	// 			|| $this->_input_stream === $this->raw_input_stream
	// 		) {
	// 			// 按 字符串 解析 为 数组
	// 			parse_str($this->raw_input_stream, $this->_input_stream);
	// 		}

	// 		is_array($this->_input_stream) OR $this->_input_stream = array();
	// 	}

	// 	return $this->_fetch_from_array($this->_input_stream, $index, $xss_clean);
	// }


// ------------------------------- 扩展 -------------------------------
	/**
	 * PUT 请求方法 输入项
	 * 
	 * @param string $key
	 * @param bool $xss_clean
	 * @return mixed
	 */
	public function put($key = NULL, $xss_clean = NULL)
	{
		return $this->input_stream($key, $xss_clean);
	}

	/**
	 * DELETE 请求方法 输入项
	 * 
	 * @param string $key
	 * @param bool $xss_clean
	 * @return mixed
	 */
	public function delete($key = NULL, $xss_clean = NULL)
	{
		return $this->input_stream($key, $xss_clean);
	}

	/**
	 * 检索 输入项
	 * 
	 * @param string|null $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function input($key = null, $default = null)
	{
		$input = $this->{$this->method()}();

		if (is_null($key)) {
			return $input;
		}

		return $input[$key] ?? $default;
	}

	/**
	 * 检索 批量 输入项
	 * 
	 * @param array|mixed|null
	 * @param array
	 */
	public function all($keys = null)
	{
		$input = $this->input();

		if (is_null($keys)) {
			return $input;
		}

		$results = [];

		foreach (is_array($keys) ? $keys : func_get_args() as $key) {
			$results[$key] = $input[$key] ?? null;
		}

		return $results;
	}

}
