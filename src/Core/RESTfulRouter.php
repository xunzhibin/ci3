<?php
namespace Xzb\Ci3\Core;

use Xzb\Ci3\Helpers\Str;
use Xzb\Ci3\Http\Exception\NotFoundException;

abstract class RESTfulRouter extends \CI_Router
{
	/**
	 * 请求方法
	 * 
	 * @var string
	 */
	protected $requestMethod;

// ---------------------------------- 重构 ----------------------------------
	/**
	 * 构造函数
	 * 
	 * @param array $routing
	 * @return void
	 */
	public function __construct($routing = NULL)
	{
		$this->requestMethod = strtolower($_SERVER['REQUEST_METHOD'] ?? 'cli');
		$this->directory = $this->parseAppControllerDir();

		parent::__construct($routing);

		$this->set_method(
			$this->parseRESTfulMethod($this->method)
		);

	}

	/**
	 * 解析 路由
	 * 
	 * @return void
	 */
	protected function _parse_routes()
	{
		if ($routes = $this->getAppRoutes()) {
			$this->default_controller = $routes['default_controller'] ?? $this->default_controller;
			$this->translate_uri_dashes = $routes['translate_uri_dashes'] ?? $this->translate_uri_dashes;

			unset($routes['default_controller'], $routes['translate_uri_dashes']);

			$this->routes = $routes;
			// $this->routes = array_merge($this->routes, $this->getAppRoutes());
		}

		$this->parseRoutes();
		// parent::_parse_routes();
	}

	/**
	 * 验证 请求
	 * 
	 * @param array $segments
	 * @return array
	 */
	protected function _validate_request($segments)
	{
		// return parent::_validate_request($segments);
		return $this->validateRequest($segments);
	}

// ---------------------------------- 扩展 ----------------------------------
	/**
	 * 解析 RESTful 方法
	 * 
	 * @param string $method
	 * @return string
	 */
	protected function parseRESTfulMethod(string $method): string
	{
		if (is_cli()) {
			return $method;
		}

		if ($method === 'index') {
			$method = $this->parseRESTfulMethodOfIndexMethod();
		}

		return $method . '_' . $this->requestMethod;
	}

	/**
	 * 解析 index方法 的 RESTful方法
	 * 
	 * @return string
	 */
	protected function parseRESTfulMethodOfIndexMethod(): string
	{
		$methods = [
			'post' => 'store',
			'put' => 'update',
			'delete' => 'destroy',
		];

		if ($method = $methods[$this->requestMethod] ?? null) {
			return $method;
		}

		if ($this->requestMethod == 'get' && array_intersect_key($_GET, array_flip(['id', 'guid']))) {
			return 'show';
		}

		return 'index';
	}

	/**
	 * 获取 应用 路由
	 * 
	 * @return array
	 * 
	 * @throws \Xzb\Ci3\Http\Exception\NotFoundException
	 */
	protected function getAppRoutes(): array
	{
		$routePath = implode(DIRECTORY_SEPARATOR, [
			realpath(APPPATH . '..'),
			config_item('system'),
			Str::upperCamelCase((string)config_item('app')),
			'Routes',
			config_item('client_type') . '.php'
		]);

		if (file_exists($routePath)) {
			include($routePath);

			return isset($route) && is_array($route) ? $route : [];
		}

		throw new NotFoundException(sprintf('The routing file %s does not exist', $routePath));
	}

	/**
	 * 解析 应用 控制器 目录
	 * 
	 * @return string
	 */
	protected function parseAppControllerDir(): string
	{
		return implode('/', [
			'..',
			'..',
			config_item('system'),
			'Modules',
			Str::upperCamelCase(config_item('module')),
			'Http',
			'Controllers',
		]) . '/';
	}

	/**
	 * 解析 路由
	 * 
	 * @return void
	 * 
	 * @throws \Xzb\Ci3\Http\Exception\NotFoundException
	 */
	protected function parseRoutes()
	{
		$uri = implode('/', $this->uri->segments);

		foreach ($this->routes as $key => $val) {
			if (is_array($val)) {
				$val = array_change_key_case($val, CASE_LOWER);

				if (isset($val[$this->requestMethod])) {
					$val = $val[$http_verb];
				}
				else {
					continue;
				}
			}

			$key = str_replace(array(':any', ':num'), array('[^/]+', '[0-9]+'), $key);

			if (preg_match('#^'.$key.'$#', $uri, $matches)) {
				if ( ! is_string($val) && is_callable($val)) {
					array_shift($matches);

					$val = call_user_func_array($val, $matches);
				}
				else if (strpos($val, '$') !== FALSE && strpos($key, '(') !== FALSE) {
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}

				$this->_set_request(explode('/', $val));

				return;
			}
		}

		throw new NotFoundException(sprintf('The route %s could not be found.', $uri));
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
			$filePath = $this->directory . ucfirst(
				$this->translate_uri_dashes === TRUE ? str_replace('-', '_', $segments[0]) : $segments[0]
			) . '.php';
			$dirPath = $this->directory . $segments[0];

			if (file_exists($file = APPPATH . 'controllers/' . $filePath)) {
				return $segments;
			}
			else if (is_dir($dir = APPPATH . 'controllers/' . $dirPath)) {
				$this->set_directory(array_shift($segments), TRUE);
				continue;
			}

			throw new NotFoundException(sprintf('The path %s is not a file, and path %s is not a directory either', $file, $dir));
		}
	}

}
