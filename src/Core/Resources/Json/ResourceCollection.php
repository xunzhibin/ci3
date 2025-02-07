<?php

namespace Xzb\Ci3\Core\Resources\Json;

use Xzb\Ci3\Core\Eloquent\Collection;

class ResourceCollection extends Resource
{
	/**
	 * 构造函数
	 * 
	 * @param mixed $resource
	 * @return void
	 */
	public function __construct($resource)
	{
		parent::__construct($resource);

		$this->collection = is_array($resource) ? new Collection($resource) : $resource;
	}

	/**
	 * 转为 数组
	 * 
	 * @return array
	 */
	public function toArray()
	{
		return $this->collection->toArray();
	}

}
