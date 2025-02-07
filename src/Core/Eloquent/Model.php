<?php

namespace Xzb\Ci3\Core\Eloquent;

use Xzb\Ci3\Helpers\Traits\ForwardsCalls;
// use Iterator;
use ArrayAccess;
// use JsonSerializable;

// abstract class Model implements ArrayAccess, JsonSerializable
abstract class Model implements ArrayAccess
// abstract class Model implements Iterator
// abstract class Model
{
	use Traits\Attributes;
	use Traits\Timestamps;
	use Traits\ModelOperations;
	use Traits\Events;
	use ForwardsCalls;

	/**
	 * 关联 数据表 类
	 * 
	 * @var string
	 */
	protected $table;

	/**
	 * 模型 在数据表中 是否存在
	 * 
	 * @var bool
	 */
	public $exists = false;

	/**
	 * 是否为 当前生命周期内创建
	 * 
	 * @var bool
	 */
	public $wasRecentlyCreated = false;

	/**
	 * 数据表类 返回的方法
	 * 
	 * @var array
	 */
	protected $tablePassthru = [
		'getConnection',
		'getTable',
		'getColumns',
		'hasColumn',
		'getColumnType',
		'isColumnNullable',
		'getCreatedAtColumn',
		'getUpdatedAtColumn',
		'isAutoIncrement',
		'getPrimaryKeyName',
		'getForeignKeyName'
	];

	/**
	 * 填充 属性
	 * 
	 * @param array $attributes
	 * @return $this
	 */
	public function fill(array $attributes)
	{
		foreach ($attributes as $key => $value) {
			$this->setAttribute($key, $value);
		}

		return $this;
	}

	/**
	 * 创建 模型 新实例
	 * 
	 * @param array $attributes
	 * @param bool $exists
	 * @return static
	 */
	public function newInstance(array $attributes = [], bool $exists = false)
	{
		$model = new static;

		// 是否存在
		$model->exists = $exists;

		// 填充 属性
		$model->fill($attributes);

		return $model;
	}

	/**
	 * 创建 模型 新实例
	 * 
	 * @param array $attributes
	 * @param bool $exists
	 * @param bool $isSync
	 * @return static
	 */
	public function newRawInstance($attributes = [], $exists = false, $isSync = false)
	{
		$model = $this->newInstance([], $exists);

		$model->setRawAttributes((array)$attributes, $isSync);

		return $model;
	}

	/**
	 * 创建 模型集合 新实例
	 * 
	 * @param array $models
	 * @return \Xzb\Ci3\Core\Eloquent\Collection
	 */
	public function newCollection(array $models = [])
	{
		return new Collection($models);
	}

	/**
	 * 转换为 数组
	 * 
	 * @return array
	 */
	public function toArray(): array
	{
		return $this->attributesToArray();
	}

// // ---------------------- PHP JsonSerializable(JSON序列化) 预定义接口 ----------------------
// 	/**
// 	 * 转为 JSON可序列化的数据
// 	 *
// 	 * @return mixed
// 	 */
// 	public function jsonSerialize()
// 	{
// 		return $this->toArray();
// 	}

// ---------------------- PHP ArrayAccess(数组式访问) 预定义接口 ----------------------
	/**
	 * 模型属性 是否存在
	 * 
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset): bool
	{
		return array_key_exists($offset, $this->attributes);
	}

	/**
	 * 获取 模型属性
	 * 
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->getAttribute($offset);
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
		$this->setAttribute($offset, $value);
	}

	/**
	 * 销毁 模型属性
	 * 
	 * @param mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset): void
	{
		unset($this->attributes[$offset]);
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
		return $this->getAttribute($key);
	}

	/**
	 * 动态 设置 属性
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function __set(string $key, $value): void
	{
		$this->setAttribute($key, $value);
	}

	/**
	 * 处理调用 不可访问 方法
	 * 
	 * @param string $method
	 * @param array $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (in_array($method, $this->tablePassthru)) {
			return $this->forwardCallTo(new $this->table, $method, $parameters);
		}

		return $this->forwardCallTo((new Builder())->setModel($this), $method, $parameters);
	}

	/**
	 * 处理调用 不可访问 静态方法
	 * 
	 * @param string $method
	 * @param array $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		return (new static)->$method(...$parameters);
	}

}
