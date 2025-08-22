<?php
namespace Xzb\Ci3\Http\Foundation;

use IteratorAggregate;
use ArrayIterator;
use Countable;

class ParameterBag implements IteratorAggregate, Countable
{
	/**
	 * 参数
	 * 
	 * @var array
	 */
	protected $parameters;

	/**
	 * 构造函数
	 * 
	 * @param array $parameters
	 * @return void
	 */
	public function __construct(array $parameters = [])
	{
		$this->parameters = $parameters;
	}

	/**
	 * 获取
	 * 
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get(string $key, $default = null)
	{
		return $this->parameters[$key] ?? $default;
	}

	/**
	 * 设置
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set(string $key, $value)
	{
		$this->parameters[$key] = $value;
	}

	/**
	 * 删除
	 * 
	 * @param string $key
	 * @return void
	 */
	public function remove(string $key)
	{
		unset($this->parameters[$key]);
	}

	/**
	 * 添加
	 * 
	 * @param array $parameters
	 * @return void
	 */
	public function add(array $parameters)
	{
		foreach ($parameters as $key => $value) {
			$this->set($key, $value);
		}
	}

	/**
	 * 所有
	 * 
	 * @return array
	 */
	public function all(): array
	{
		return $this->parameters;
	}

	/**
	 * 替换
	 * 
	 * @param array $parameters
	 * @return void
	 */
	public function replace(array $parameters = [])
	{
		$this->parameters = [];
		$this->add($parameters);
	}

	/**
	 * 键名
	 * 
	 * @return array
	 */
	public function keys(): array
	{
		return array_keys($this->parameters);
	}

	/**
	 * 是否存在
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function has(string $key): bool
	{
		return array_key_exists($key, $this->parameters);
	}

// ------------------------------- IteratorAggregate(聚合式迭代器) 接口 -------------------------------
	/**
	 * 获取 迭代器
	 * 
	 * @return \ArrayIterator
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->parameters);
	}

// ------------------------------- Countable 接口 -------------------------------
	/**
	 * 统计个数
	 * 
	 * @return int
	 */
	public function count(): int
	{
		return count($this->parameters);
	}

}
