<?php

namespace Xzb\Ci3\Core;

abstract class Router extends \CI_Router
{
	/**
	 * 模块 目录
	 * 
	 * @var string
	 */
	protected $moduleDir;

// ---------------------------------- 重构 ----------------------------------
	/**
	 * 构造函数
	 */
	public function __construct($routing = NULL)
	{
		$this->moduleDir = config_item('module_dir');

		parent::__construct($routing);

		if ($this->method === 'index') {
			$this->set_method($this->method);
		}
	}

	/**
	 * 设置  方法 名称
	 * 
	 * @param string $method
	 * @return void
	 */
	public function set_method($method)
	{
		parent::set_method(
			config_item('restful') ? $this->generateRestMethod($method) : $method
		);
	}

	/**
	 * 验证 请求
	 * 
	 * @param array $segments
	 * @return array
	 */
	protected function _validate_request($segments)
	{
		if ($this->moduleDir && $moduleSegments = $this->validateRequest($segments)) {
			$this->directory = $this->moduleDir;
	
			return $moduleSegments;
		}

		return parent::_validate_request($segments);
	}

// ---------------------------------- 扩展 ----------------------------------
	/**
	 * 拼接 模块 目录
	 * 
	 * @param string 
	 * @return $this
	 */
	protected function spliceModuleDir(string $dir)
	{
		$this->moduleDir .= trim($dir, '/') . '/';

		return $this;
	}

	/**
	 * 验证 请求
	 * 
	 * @param array $segments
	 * @return array
	 */
	protected function validateRequest(array $segments): array
	{
		$c = count($segments);

		while ($c-- > 0) {
			$segment = $this->translate_uri_dashes === TRUE ? str_replace('-', '_', $segments[0]) : $segments[0];
			$test = $this->moduleDir . ucfirst($segment);

			if (file_exists(APPPATH . 'controllers/' . $test . '.php')) {
				return $segments;
			}
			else if (is_dir(APPPATH . 'controllers/' . $this->moduleDir . $segments[0])) {
				$this->spliceModuleDir(array_shift($segments));
				continue;
			}

			return [];
		}

		return [];
	}

	/**
	 * 生成 rest 方法
	 * 
	 * @param string $method
	 * @return string
	 */
	protected function generateRestMethod(string $method): string
	{
		if (is_cli()) {
			return $method;
		}

		$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
		if ($method === 'index') {
			$method = $this->parseRemapMethod($requestMethod);
		}

		return $method . '_' . $requestMethod;
	}

	/**
	 * 解析 index方法 映射 rest方法
	 * 
	 * @param string $requestMethod
	 * @return string
	 */
	protected function parseRemapMethod(string $requestMethod): string
	{
		switch ($requestMethod) {
			case 'post':
				return 'store';
			case 'put':
				return 'update';
			case 'delete':
				return 'destroy';
			case 'get':
				if (array_intersect_key($_GET, array_flip(['id', 'guid']))) {
					return 'show';
				}

				return 'index';
		}
	}

// ---------------------------------- 魔术方法 ----------------------------------

	
}
