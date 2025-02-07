<?php

namespace Xzb\Ci3\Core\Resources\Json;

use Xzb\Ci3\Core\Eloquent\{
	Collection, Paginator
};

class PaginatedResource extends Resource
{
	/**
	 * meta 元数据 项
	 * 
	 * @var array
	 */
	public static $metaItems;

	/**
	 * 构造函数
	 * 
	 * @param mixed $resource
	 * @return void
	 */
	public function __construct($resource)
	{
		parent::__construct($resource);

		$this->collection = $this->collectionResource($resource);
	}

	/**
	 * 资源集合
	 * 
	 * @param mixed $resource
	 * @return mixed
	 */
	protected function collectionResource($resource)
	{
		if (is_array($resource)) {
			return new Collection($resource[$this->wrapper()] ?? $resource);
		}

		return is_array($collection = $resource->getCollection())
					? new Collection($collection) : $collection;
	}

	/**
	 * 创建 HTTP 响应
	 * 
	 * @return array
	 */
	public function toResponse()
	{
		return [
			$this->wrap(
				$this->toArray(),
				array_merge_recursive(
					$this->paginationInfo(),
					$this->with(),
					$this->additional
				)
			),
			$this->reponseStatusCode()
		];
	}

	/**
	 * 分页 信息
	 * 
	 * @return array
	 */
	protected function paginationInfo()
	{
		$meta = static::$metaItems
					? array_intersect_key($this->meta(), array_flip(static::$metaItems))
					: $this->meta();

		return [
			'meta' => $meta,
		];
	}

	/**
	 * 元 数据
	 * 
	 * @return array
	 */
	protected function meta()
	{
		if ($this->resource instanceof Paginator) {
			return $this->resource->paginationInfo();
		}

		return array_intersect_key($this->resource['meta'] ?? $this->resource, array_flip([
			'total', 'count', 'total_rows',
			'per_page', 'current_page', 'page',
			'last_page', 'total_page',
		]));
	}

}
