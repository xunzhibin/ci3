<?php

namespace Xzb\Ci3\Core\Eloquent;

use Xzb\Ci3\Database\Connection;
use Xzb\Ci3\Helpers\{
	Str
};

class Builder
{
	/**
	 * 操作 模型 实例
	 * 
	 * @var \Xzb\Ci3\Core\Eloquent\Model
	 */
	protected $model;	

	/**
	 * CI查询构造器 类方法
	 * 
	 * @var array
	 */
	protected $ciQb = [];

	/**
	 * 设置 模型
	 * 
	 * @param \Xzb\Ci3\Core\Eloquent\Model $model
	 * @return $this
	 */
	public function setModel(Model $model)
	{
		$this->model = $model;

		$this->from($model->getTable());

		return $this;
	}

	/**
	 * 获取 模型
	 * 
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * 创建 模型 新实例
	 * 
	 * @param array $attributes
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 */
	public function newModelInstance(array $attributes = [])
	{
		return $this->model->newInstance($attributes);
	}

	/**
	 * 获取 CI 原始 查询构造器类
	 * 
	 * @param bool $read
	 * @return \CI_DB_query_builder
	 */
	public function db(bool $read = false)
	{
		return (new Connection)->query(
			$this->model->getConnection($read)
		);
	}

	/**
	 * 设置 CI查询构造器 类方法
	 * 
	 * @param string $method
	 * @param array $parameters
	 * @return $this
	 */
	protected function setCiQb(string $method, array $parameters)
	{
		array_push($this->ciQb, compact('method', 'parameters'));

		return $this;
	}

	/**
	 * 执行 CI查询构造器 类方法
	 * 
	 * @param \CI_DB_query_builder
	 * @return \CI_DB_query_builder
	 */
	protected function performCiQb(\CI_DB_query_builder $builder)
	{
		foreach ($this->ciQb as $value) {
			extract($value);
			$builder->{$method}(...$parameters);
		}

		return $builder;
	}

	/**
	 * 插入
	 * 
	 * @param array $values
	 * @return int
	 */
	public function insert(array $values): int
	{
		foreach ($values as &$value) {
			$value = $this->newModelInstance($value)->getAttributesForInsert();
		}

		return $this->performCiQb($this->db())->insert($values);
	}

	/**
	 * 创建
	 * 
	 * @param array $attributes
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 */
	public function create(array $attributes = [])
	{
		$model = $this->newModelInstance($attributes);

		$model->save();

		return $model;
	}

	/**
	 * 更新
	 * 
	 * @param array $value
	 * @return int
	 */
	public function update(array $value): int
	{
		$value = $this->newModelInstance($value)->getAttributesForUpdate();

		return $this->performCiQb($this->db())->update($value);
	}

	/**
	 * 读取 记录
	 * 
	 * @param array|string $columns
	 * @return \Xzb\Ci3\Core\Eloquent\Collection
	 */
	public function get($columns = ['*'])
	{
		$results = $this->performCiQb($this->db(true))
						->get(is_array($columns) ? $columns : func_get_args())
						->result_array();

		$instance = $this->newModelInstance();

		return $instance->newCollection(array_map(function ($item) use ($instance) {
			return $instance->newRawInstance($item, true, true);
		}, $results));
	}

	/**
	 * 读取 第一条记录
	 * 
	 * @param array|string
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 */
	public function first($columns = ['*'])
	{
		return $this->limit(1)->get($columns)->first();
	}

	/**
	 * 读取 唯一记录
	 * 
	 * @param array|string $columns
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 * 
	 * @throws \Xzb\Ci3\Core\Eloquent\ModelNotFoundException
	 * @throws \Xzb\Ci3\Core\Eloquent\MultipleRecordsFoundException
	 */
	public function sole($columns = ['*'])
	{
		$result = $this->limit(2)->get($columns);

		$count = $result->count();
		if ($count === 0) {
			throw (new ModelNotFoundException('No query results for model [' . get_class($this->model) . '] '));
		}

		if ($count > 1) {
			throw new MultipleRecordsFoundException($count . ' records were found');
		}

		return $result->first();
	}

	/**
	 * 偏移量 分页
	 * 
	 * @param int $perPage
	 * @param array $columns
	 * @param string $pageName
	 * @param int $page
	 * @return \Xzb\CI3\Core\Eloquent\Paginator
	 */
	public function offsetPaginate(int $perPage = null, array $columns = ['*'], $pageName = 'page', $page = null)
	{
		$page = $page ?: Paginator::resolveCurrentPage($pageName);
		$perPage = $perPage ?: Paginator::defaultPerPage();

		$total = $this->count();

		$results = $total
					? $this->forPage($page, $perPage)->get($columns)
					: $this->model->newCollection();

		return new Paginator($results, $total, $perPage, $page);
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
		$ciQbMethods = [
			'from', 'where', 'likeGroup', 'orderBy', 'limit', 'forPage', 
		];
		if (in_array($method, $ciQbMethods)) {
			$this->setCiQb($method, $parameters);
			return $this;
		}

		// 写入
		$ciQbWriteMethods = [
			'insertGetId', 'delete',
		];
		if (in_array($method, $ciQbWriteMethods)) {
			return $this->performCiQb($this->db())->{$method}(...$parameters);
		}

		// 读取
		$ciQbWriteMethods = [
			'count', 'max'
		];
		if (in_array($method, $ciQbWriteMethods)) {
			return $this->performCiQb($this->db(true))->{$method}(...$parameters);
		}

		throw new \BadMethodCallException(
			sprintf('Call to undefined method %s::%s()', static::class, $method)
		);
	}

}
