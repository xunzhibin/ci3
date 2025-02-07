<?php

namespace Xzb\Ci3\Core\Resources\Json;

use ArrayAccess;

class JsonResource extends Resource implements ArrayAccess
{

// ---------------------- PHP ArrayAccess(数组式访问) 预定义接口 ----------------------
	/**
	 * 模型属性 是否存在
	 * 
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset): bool
	{
		return isset($this->resource[$offset]);
	}

	/**
	 * 获取 模型属性
	 * 
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->resource[$offset];
	}

	/**
	 * 设置 模型属性
	 * 
	 * @param mixed $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value): void
	{
		$this->resource[$offset] = $value;
	}

	/**
	 * 销毁 模型属性
	 * 
	 * @param mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset): void
	{
		unset($this->resource[$offset]);
	}

// ---------------------- 魔术方法 ----------------------
	/**
	 * 动态 获取 属性
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function __get(string $key)
	{
		if (is_array($this->resource)) {
			return $this->resource[$key];
		}

		return $this->resource->{$key};
	}

}
