<?php

namespace Xzb\Ci3\Core\Eloquent;

class Collection
{
	/**
	 * 集合 项
	 * 
	 * @var array
	 */
	protected $items = [];

	/**
	 * 构造函数
	 * 
	 * @param array $items
	 * @return void
	 */
	public function __construct(array $items = [])
	{
		$this->items = $items;
	}

	/**
	 * 获取 所有 项
	 * 
	 * @return array
	 */
	public function all()
	{
		return $this->items;
	}

	/**
	 * 获取 第一个 项
	 * 
	 * @param mixed $default
	 * @return mixed
	 */
	public function first($default = null)
	{
		return reset($this->items) ?: $default;
	}

	/**
	 * 映射项
	 * 
	 * @param callable
	 * @return static
	 */
	public function map(callable $callback)
	{
		return new static(array_map($callback, $this->items));
	}

	/**
	 * 总数
	 * 
	 * @return int
	 */
	public function count(): int
	{
		return count($this->items);
	}

	/**
	 * 转为 数组
	 * 
	 * @return array
	 */
	public function toArray(): array
	{
		return array_map(function ($item) {
			return $item->toArray();
		}, $this->items);
	}

}
