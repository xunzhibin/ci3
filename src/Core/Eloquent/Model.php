<?php

namespace Xzb\Ci3\Core\Eloquent;

abstract class Model
{
	use Traits\Attributes;
	use Traits\Timestamps;
	use Traits\ModelOperations;

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

// ---------------------- 魔术方法 ----------------------
	/**
	 * 处理调用 不可访问 方法
	 * 
	 * @param string $method
	 * @param array $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		// 表 方法
		$tableMethods = [
			'getConnection', 'getTable',
			'getColumns', 'hasColumn', 'getColumnType', 'isColumnNullable',
			'getCreatedAtColumn', 'getUpdatedAtColumn',
			'isAutoIncrement', 'getPrimaryKeyName'
		];
		if (in_array($method, $tableMethods)) {
			return (new $this->table)->{$method}(...$parameters);
		}

		$builder = (new Builder())->setModel($this);
		try {
			return $builder->{$method}(...$parameters);
		}
		catch (\Error|\BadMethodCallException $e) {
			$pattern = '~^Call to undefined method (?P<class>[^:]+)::(?P<method>[^\(]+)\(\)$~';
			if (! preg_match($pattern, $e->getMessage(), $matches)) {
				throw $e;
			}

			if ($matches['class'] != get_class($builder) || $matches['method'] != $method) {
				throw $e;
			}

			throw new \BadMethodCallException(
				sprintf('Call to undefined method %s::%s()', static::class, $method)
			);
		}
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
