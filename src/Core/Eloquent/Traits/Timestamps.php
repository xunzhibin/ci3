<?php

namespace Xzb\Ci3\Core\Eloquent\Traits;

use Xzb\Ci3\Helpers\{
	Date
};

trait Timestamps
{
	/**
	 * 是否 动态维护 操作时间
	 * 
	 * @var bool
	 */
	protected $timestamps = true;

	/**
	 * 是否 动态维护 操作时间
	 * 
	 * @return bool
	 */
	public function usesTimestamps(): bool
	{
		return $this->timestamps;
	}

	/**
	 * 更新 时间戳
	 * 
	 * @return $this
	 */
	public function updateTimestamps()
	{
		if (! $this->usesTimestamps()) {
			return $this;
		}

		$time = Date::now();

		// 更新时间
		$updatedAtColumn = $this->getUpdatedAtColumn();
		if ($updatedAtColumn && ! $this->isEdited($updatedAtColumn)) {
			$this->setAttribute($updatedAtColumn, $time);
		}

		// 创建时间
		$createdAtColumn = $this->getCreatedAtColumn();
		if ( ! $this->exists && $createdAtColumn && ! $this->isEdited($createdAtColumn)) {
			$this->setAttribute($createdAtColumn, $time);
		}

		return $this;
	}

}
