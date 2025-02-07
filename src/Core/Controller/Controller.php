<?php

namespace Xzb\Ci3\Core\Controller;

use Xzb\Ci3\Core\Exceptions\Http\NotFoundException;
use Xzb\Ci3\Core\Request;
use Xzb\Ci3\Core\Resources\Json\Resource;

abstract class Controller extends \CI_Controller
{
	/**
	 * 模型 类
	 * 
	 * @var string
	 */
	protected $model;

	/**
	 * 请求 类
	 * 
	 * @var array
	 */
	protected $requests = [
		// '{请求URI}' => [
		// 	'{控制器方法}' => {类}::class
		// ]
		// 'user' => ['update_put' => UserRequest::class]
	];

	/**
	 * 资源 类
	 * 
	 * @var array
	 */
	public $resources = [
		// '{请求URI}' => [
		// 	'{控制器方法}' => {类}::class
		// ]
		// 'user' => ['update_put' => UserRequest::class]
	];

	/**
	 * 重映射方法
	 * 
	 * @param string $method
	 * @param array $param
	 * @return void
	 */
	public function _remap(string $method, array $params = [])
	{
		if (! method_exists($this, $method)) {
			throw new NotFoundException(
				'Not Found: ' . $this->router->directory . $this->router->class . '/' . $method
			);
		}

		array_push($params, $request = $this->newRequestInstance());

		$resource = call_user_func_array([$this, $method], $params);

		if (! is_null($resource)) {
			$this->output->response(
				...$this->newResourceInstance($resource)->toResponse()
			);
		}
	}

	/**
	 * 新建 请求类 实例化
	 * 
	 * @return \Xzb\Ci3\Core\Request
	 */
	protected function newRequestInstance()
	{
		$class = $this->requestClass();

		return new $class;
	}

	/**
	 * 请求类
	 * 
	 * @return string
	 */
	protected function requestClass()
	{
		return $this->restfulUriMatchClass($this->requests ?: [])
					?: $this->defaultRequest();
	}

	/**
	 * 默认 请求类
	 * 
	 * @return string
	 */
	protected function defaultRequest()
	{
		return Request::class;
	}

	/**
	 * 新建 资源类 实例化
	 * 
	 * @param mixed $resource
	 * @return \Xzb\Ci3\Core\Resources\Json\JsonResource
	 */
	protected function newResourceInstance($resource)
	{
		$class = $this->resourceClass();

		return new $class($resource);
	}

	/**
	 * 资源类
	 * 
	 * @return string
	 */
	protected function resourceClass()
	{
		return $this->restfulUriMatchClass($this->resources ?: [])
					?: $this->defaultResource();
	}

	/**
	 * 默认资源类
	 * 
	 * @return string
	 */
	protected function defaultResource()
	{
		return Resource::class;
	}

	/**
	 * restful URI 匹配类
	 * 
	 * @param array $class
	 * @return string
	 */
	protected function restfulUriMatchClass(array $classes): string
	{
		$requestUri = $this->uri->uri_string();
		$method = $this->router->method;

		foreach ($classes as $uri => $value) {
			if (preg_match('#^' . $uri . '$#', $requestUri, $matches)) {
				if (array_key_exists($method, $value) && $value[$method]) {
					return $value[$method];
				}
			}
		}

		return '';
	}

}
