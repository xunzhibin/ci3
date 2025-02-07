<?php

namespace Xzb\Ci3\Core\Eloquent;

class Paginator
{
	/**
	 * 默认 每页条数
	 * 
	 * @var int
	 */
	protected static $defalutPerPage = 15;

	/**
	 * 分页 项
	 * 
	 * @var \Xzb\Ci3\Database\Collection
	 */
	protected $items;

	/**
	 * 项 总数
	 * 
	 * @var int
	 */
	protected $total;

	/**
	 * 每页显示条数
	 * 
	 * @var int
	 */
	protected $perPage;

	/**
	 * 当前页码
	 * 
	 * @var int
	 */
	protected $currentPage;

	/**
	 * 最后页码
	 * 
	 * @var int
	 */
	protected $lastPage;

	/**
	 * 构造函数
	 * 
	 * @param \Xzb\Ci3\Core\Eloquent\Collection $items
	 * @param int $total
	 * @param int $perPage
	 * @param int $currentPage
	 * @return void
	 */
	
	public function __construct(Collection $items, int $total, int $perPage, int $currentPage)
	{
		$this->items		= $items;
		$this->total		= $total;
		$this->perPage		= $perPage;
		$this->currentPage	= $currentPage;
		$this->lastPage		= max((int) ceil($total / $perPage), 1);
	}

	/**
	 * 获取 分页 数据集合
	 * 
	 * @return \Xzb\Ci3\Core\Eloquent\Collection
	 */
	public function getCollection()
	{
		return $this->items;
	}

	/**
	 * 获取 项总数
	 * 
	 * @return int
	 */
	public function total(): int
	{
		return $this->total;
	}

	/**
	 * 获取 每页显示条数
	 * 
	 * @return int
	 */
	public function perPage(): int
	{
		return $this->perPage;
	}

	/**
	 * 获取 当前页码
	 * 
	 * @return int
	 */
	public function currentPage(): int
	{
		return $this->currentPage;
	}

	/**
	 * 获取 最后页码
	 * 
	 * @return int
	 */
	public function lastPage(): int
	{
		return $this->lastPage;
	}

	/**
	 * 转为 数组
	 * 
	 * @return array
	 */
	public function toArray(): array
	{
		// return [
		// 	'data' => $this->items->toArray(),
		// 	'total'			=> $this->total(),
		// 	'count'			=> $this->total(),
		// 	'per_page'		=> $this->perPage(),
		// 	'current_page'	=> $this->currentPage(),
		// 	'last_page'		=> $this->lastPage(),
		// 	'total_page'	=> $this->lastPage(),
		// ];

		return array_merge([
			'data' => $this->items->toArray(),
		], $this->paginationInfo());
	}

	/**
	 * 分页 信息
	 * 
	 * @return array
	 */
	public function paginationInfo(): array
	{
		return [
			'total_rows'	=> $this->total(),
			'total'			=> $this->total(),
			'count'			=> $this->total(),

			'page'			=> $this->currentPage(),
			'current_page'	=> $this->currentPage(),
			'per_page'		=> $this->perPage(),

			'last_page'		=> $this->lastPage(),
			'total_page'	=> $this->lastPage(),
		];
	}

	/**
	 * 解析 当前页
	 * 
	 * @param string $pageName
	 * @param int $default
	 * @return int
	 */
	public static function resolveCurrentPage($pageName = 'page', $default = 1): int
	{
		$page = $_GET[$pageName] ?? null;

		if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int)$page >= 1) {
			return (int)$page;
		}

		return $default;
	}

	/**
	 * 显示条数
	 * 
	 * @return int
	 */
	public static function defaultPerPage(): int
	{
		return static::$defalutPerPage;
	}

}
