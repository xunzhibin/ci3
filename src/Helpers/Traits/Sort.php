<?php

namespace Xzb\Ci3\Helpers\Traits;

trait Sort
{
	/**
	 * 排序 类型
	 * 		arithmetic(算术运算符)
	 * 		direction(排序方向)
	 * 
	 * @var string
	 */
	protected $sortType;

	/**
	 * 排序 分隔符
	 * 
	 * @var string
	 */
	protected $sortDelimiter = ',';

	/**
	 * 算术运算符 映射 排序方向
	 * 
	 * @var array
	 */
	protected $arithmeticMappingDrctn = [
		'+' => 'asc', // 升序
		'-' => 'desc', // 降序
	];

	/**
	 * 排序方向 分隔符
	 * 
	 * @var string
	 */
	protected $drctnDelimiter = '|';

	/**
	 * 解析 排序
	 * 
	 * @param string $sort
	 * @return array
	 */
	public function parseSort(string $sort = null): array
	{
		if (! strlen($sort)) {
			return [];
		}

		switch ($this->sortType) {
			// 排序方向
			case 'direction':
				return $this->directionSort($sort);
			// 算术运算符
			case 'arithmetic':
			default:
				return $this->arithmeticSort($sort);
		}
	}

	/**
	 * 算术运算符 排序
	 * 
	 * @param string $sort
	 * @return array
	 */
	protected function arithmeticSort(string $sort): array
	{
		$orderBy = [];
		foreach (explode($this->sortDelimiter, $sort) ?: [] as $field) {
			foreach ($this->arithmeticMappingDrctn as $mapKey => $direction) {
				if (strncmp($field, $mapKey, strlen($mapKey)) === 0) {
					if ($column = current(array_filter(explode($mapKey, $field, 2)))) {
						$orderBy[$column] = $direction;
					}
				}
			}
		}

		return $orderBy;
	}

	/**
	 * 排序方向 排序
	 * 
	 * @param string $sort
	 * @return array
	 */
	protected function directionSort(string $sort)
	{
		$orderBy = [];
		foreach (explode($this->sortDelimiter, $sort) ?: [] as $field) {
			$segment = explode($this->drctnDelimiter, $field);

			if (count($segment) != 2) {
				continue;
			}

			list($column, $direction) = $segment;

			if (! in_array($direction, $this->arithmeticMappingDrctn)) {
				continue;
			}

			$orderBy[$column] = $direction;
		}

		return $orderBy;
	}
}
