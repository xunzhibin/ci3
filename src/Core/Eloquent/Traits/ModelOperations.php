<?php

namespace Xzb\Ci3\Core\Eloquent\Traits;

use Xzb\Ci3\Core\Eloquent\{
	ModelNotExistException,
	ModelMissingPrimaryKeyException,
	ModelMissingPrimaryKeyValueException
};

trait ModelOperations
{
	/**
	 * 保存
	 * 
	 * @return bool
	 */
	public function save(): bool
	{
		// saving 保存前 事件
		// $this->fireModelEvent('saving');

		$saved  = $this->exists
					// 存在时 更新
					? $this->performUpdate()
					// 不存在时 插入
					: $this->performInsert();

		// 操作成功
		if ($saved) {
			// saved 保存后 事件
			// $this->fireModelEvent('saved');

			// 同步 属性 原始状态
			$this->syncOriginal();
		}

		return (bool)$saved;
	}

	/**
	 * 更新
	 * 
	 * @param array $attributes
	 * @return bool
	 */
	public function update(array $attributes = []): bool
	{
		// 不存在
		if (! $this->exists) {
			throw new ModelNotExistException('Not exist for update model [' . static::class . ']');
		}

		return $this->fill($attributes)->save();
	}

	/**
	 * 删除
	 * 
	 * @return int
	 */
	public function delete(): int
	{
		// 在关联数据表中 不存在
		if (! $this->exists) {
			return true;
		}

		// deleting 删除前 事件
		// $this->fireModelEvent('deleting');

		// 执行删除
		$rows = $this->performDelete();

		// deleted 删除后 事件
		// $this->fireModelEvent('deleted');

		return $rows;
	}

	/**
	 * 执行 插入
	 * 
	 * @return bool
	 */
	protected function performInsert(): bool
	{
		// creating 创建前 事件
		// $this->fireModelEvent('creating');

		// 插入 属性
		$attributes = $this->getAttributesForInsert();

		// 插入
		$id = $this->insertGetId($attributes);

		// 设置 主键
		if ($this->isAutoIncrement()) {
			$this->setAttribute($this->getPrimaryKeyName(), $id);
		}

		// 插入成功 更新为 已存在
		$this->exists = true;

		// 当前 生命周期内创建
		$this->wasRecentlyCreated = true;

		// created 创建后 事件
		// $this->fireModelEvent('created');

		return true;
	}

	/**
	 * 执行 更新
	 * 
	 * @return bool
	 */
	protected function performUpdate(): bool
	{
		// 属性 未被编辑
		if (! $this->isEdited()) {
			return false;
		}

		// updating 更新前 事件
		// $this->fireModelEvent('updating');

		// 更新 属性
		$attributes = $this->getAttributesForUpdate();
		if (count($attributes)) {
			// 更新
			$affectedRows = $this->setPrimaryKeyWhereForRUD()->update($attributes);

			// 同步 更改属性
			$this->syncChanges();
		}

		// updated 更新后 事件
		// $this->fireModelEvent('updated', false);

		return true;
	}

	/**
	 * 执行 删除
	 * 
	 * @return int
	 */
	protected function performDelete(): int
	{
		// 删除
		$rows = $this->setPrimaryKeyWhereForRUD()->delete();

		// 不存在
		$this->exists = false;

		return $rows;
	}

	/**
	 * 设置 读取(R)更新(U)删除(D)操作 主键条件
	 * 
	 * @return \CI_DB_query_builder
	 */
	protected function setPrimaryKeyWhereForRUD()
	{
		// 未设置 主键
		if (! $this->getPrimaryKeyName()) {
			throw new ModelMissingPrimaryKeyException('No primary key defined for model [' . static::class . ']');
		}

		// 主键值 不存在
		if (is_null($value = $this->getPrimaryKeyValueForRUD())) {
			throw new ModelMissingPrimaryKeyValueException('No primary key value for model [' . static::class . ']');
		}

		return $this->where($this->getPrimaryKeyName(), $value);
	}

	/**
	 * 读取(R)更新(U)删除(D)操作 主键值
	 * 
	 * @return mixed
	 */
	protected function getPrimaryKeyValueForRUD()
	{
		return $this->original[$this->getPrimaryKeyName()]
					?? $this->attributes[$this->getPrimaryKeyName()]
					?? null;
	}

}
