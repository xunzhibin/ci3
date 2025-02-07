<?php

namespace Xzb\Ci3\Core\Resources\Json;

use Xzb\Ci3\Core\Request;

class Resource
{
	/**
	 * 资源
	 * 
	 * @var mixed
	 */
	public $resource;

	/**
	 * 资源集合
	 * 
	 * @var 
	 */
	public $collection;

	/**
	 * 数据 包装器
	 * 
	 * @var string|null
	 */
	public static $wrap = 'data';

	/**
	 * 顶层 其他数据
	 * 
	 * @var array
	 */
	public $with = [];

	/**
	 * 附加数据
	 * 
	 * @var array
	 */
	public $additional = [];

	/**
	 * 构造函数
	 * 
	 * @param mixed $resource
	 * @return void
	 */
	public function __construct($resource)
	{
		$this->resource = $resource;
	}

	/**
	 * 其他数据
	 * 
	 * @return array
	 */
	public function with()
	{
		return $this->with;
	}

	/**
	 * 添加 附加数据
	 * 
	 * @param array $data
	 * @return $this
	 */
	public function additional(array $data)
	{
		$this->additional = $data;

		return $this;
	}

	/**
	 * 包装 数据
	 * 
	 * @param array $data
	 * @param array $with
	 * @param array $additional
	 * @return array
	 */
	protected function wrap(array $data, array $with = [], array $additional = [])
	{
		if ($this->wrapper() && ! array_key_exists($this->wrapper(), $data)) {
			$data = [$this->wrapper() => $data];
		}
		else if (
			(! empty($with) || ! empty($additional))
			&& (! $this->wrapper() || ! array_key_exists($this->wrapper(), $data))
		) {
			$data = [($this->wrapper() ?? 'data') => $data];
		}

		return array_merge_recursive($data, $with, $additional);
	}

	/**
	 * 包装器
	 * 
	 * @return string
	 */
	protected function wrapper()
	{
		return static::$wrap;
	}

	/**
	 * 响应 状态码
	 * 
	 * @return int
	 */
	public function reponseStatusCode()
	{
		return (new Request)->input->method() == 'post' ? 201 : 200;
	}

	/**
	 * 转为 数组
	 * 
	 * @return array
	 */
	public function toArray()
	{
		if (is_null($this->resource)) {
			return [];
		}

		return is_array($this->resource) ? $this->resource : $this->resource->toArray();
	}

	/**
	 * 创建 HTTP 响应
	 * 
	 * @return array
	 */
	public function toResponse()
	{
		return [
			$this->wrap($this->toArray(), $this->with(), $this->additional),
			$this->reponseStatusCode()
		];
	}

}
