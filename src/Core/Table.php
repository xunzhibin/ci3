<?php

namespace Xzb\Ci3\Core;

use Xzb\Ci3\Helpers\{
	Str
};

abstract class Table
{
	/**
	 * 连接组
	 * 
	 * @var string
	 */
	protected $group;

	/**
	 * 读取 连接组
	 * 
	 * @var string 
	 */
	protected $readGroup;

	/**
	 * 数据表
	 * 
	 * @var string
	 */
	protected $table;

	/**
	 * 主键
	 * 
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * 主键 是否自增
	 * 
	 * @var bool
	 */
	public $incrementing = true;

	/**
	 * 列
	 * 
	 * @var array
	 */
	protected $columns = [
		// 列名 => 数据类型
	];

	/**
	 * 创建时间 列
	 * 
	 * @var string
	 */
	const CREATED_AT = 'created_at';

	/**
	 * 更新时间 列
	 * 
	 * @var string
	 */
	const UPDATED_AT = 'updated_at';

	/**
	 * 获取 连接组
	 * 
	 * @return string
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * 获取 读取 连接组
	 * 
	 * @return string
	 */
	public function getReadGroup()
	{
		return $this->readGroup ?: $this->getGroup();
	}

	/**
	 * 获取 连接
	 * 
	 * @param bool $read
	 * @return string
	 */
	public function getConnection(bool $read = false)
	{
		return $read ? $this->getReadGroup() : $this->getGroup();
	}

	/**
	 * 获取 数据表
	 * 
	 * @return string
	 */
	public function getTable(): string
	{
		return $this->table ?: Str::snake(Str::plural(class_basename($this)));
	}

	/**
	 * 获取 主键
	 * 
	 * @return string
	 */
	public function getPrimaryKeyName(): string
	{
		return $this->primaryKey;
	}

	/**
	 * 获取 外键
	 * 
	 * @return string
	 */
	public function getForeignKeyName(): string
	{
		return Str::snake(class_basename($this)) . '_' . $this->getPrimaryKeyName();
	}

	/**
	 * 是否为 自增主键
	 * 
	 * @return bool
	 */
	public function isAutoIncrement()
	{
		return $this->incrementing;
	}

	/**
	 * 获取 列
	 * 
	 * @return array
	 */
	public function getColumns(): array
	{
		return $this->columns;
	}

	/**
	 * 获取 列
	 * 
	 * @param string $key
	 * @return array
	 */
	public function getColumn(string $key)
	{
		if (! $this->hasColumn($key)) {
			throw new Exception('Column does not exist:' . $key);
		}

		return $this->getColumns()[$key];
	}

	/**
	 * 列 是否存在  
	 * 
	 * @param string $key
	 * @return array
	 */
	public function hasColumn(string $key): bool
	{
		return array_key_exists($key, $this->getColumns());
	}

	/**
	 * 列 是否允许为 空
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function isColumnNullable(string $key): bool
	{
		return (bool)($this->getColumn($key)['null'] ?? true);
	}

	/**
	 * 获取 列 数据类型
	 * 
	 * @return string
	 */
	public function getColumnType(string $key): string
	{
		return $this->getColumn($key)['type'];
	}

	/**
	 * 获取 创建时间 列
	 * 
	 * @return string
	 */
	public function getCreatedAtColumn(): string
	{
		return static::CREATED_AT;
	}

	/**
	 * 获取 更新时间 列
	 * 
	 * @return string
	 */
	public function getUpdatedAtColumn(): string
	{
		return static::UPDATED_AT;
	}

}
