<?php

namespace Xzb\Ci3\Core\Eloquent;

use Xzb\Ci3\Database\Connection;
use Xzb\Ci3\Helpers\{
	Str
};
use Xzb\Ci3\Helpers\Traits\ForwardsCalls;
use \Closure;
use \Throwable;

class Builder
{
	use ForwardsCalls;

	/**
	 * 操作 模型 实例
	 * 
	 * @var \Xzb\Ci3\Core\Eloquent\Model
	 */
	protected $model;

	/**
	 * 事务 开启标识
	 * 
	 * @var bool
	 */
	protected static $transaction = false;

	/**
	 * 事务 查询构造器
	 * 
	 * @var \CI_DB_query_builder
	 */
	protected static $transactionDb;

	/**
	 * 从 CI查询构造器 返回写入方法
	 * 
	 * @var array
	 */
	protected $ciQbWritePassthru = [
		'insertGetId',
		'delete',
	];

	/**
	 * 从 CI查询构造器 返回读取方法
	 * 
	 * @var array
	 */
	protected $ciQbReadPassthru = [
		'count',
		'max',
		'exists'
	];

	/**
	 * CI查询构造器 缓冲 方法
	 * 
	 * @var array
	 */
	protected $ciQbBuffers = [
		'from',
		'where',
		'likeGroup',
		'orderBy',
		'limit',
		'forPage'
	];

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
	 * @param bool $exists
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 */
	public function newModelInstance(array $attributes = [], bool $exists = false)
	{
		return $this->model->newInstance($attributes, $exists);
	}

	/**
	 * 获取 CI 原始 查询构造器类
	 * 
	 * @param bool $isRead
	 * @return \CI_DB_query_builder
	 */
	public function db(bool $isRead = false)
	{
		return static::$transaction ? $this->getTransactionDb() : $this->getCiDb($isRead);
	}

	/**
	 * 获取 CI 原始 查询构造器类
	 * 
	 * @param bool $isRead
	 * @return \CI_DB_query_builder
	 */
	protected function getCiDb(bool $isRead = false)
	{
		return (new Connection)->query(
			$this->model->getConnection($isRead)
		);
	}

	/**
	 * 获取 事务 CI 原始 查询构造器类
	 * 
	 * @return \CI_DB_query_builder
	 */
	protected function getTransactionDb()
	{
		if (static::$transactionDb) {
			return static::$transactionDb;
		}

		return static::$transactionDb = $this->getCiDb();
	}

	/**
	 * 重置 事务 CI 原始 查询构造器类
	 * 
	 * @return $this
	 */
	protected function resetTransactionDb()
	{
		static::$transactionDb = null;

		return $this;
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

		return $this->performBuffer($this->db())->insert($values);
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
		$value = $this->newModelInstance($value, true)->getAttributesForUpdate();

		return $this->performBuffer($this->db())->update($value);
	}

	/**
	 * 读取 记录
	 * 
	 * @param array|string $columns
	 * @return \Xzb\Ci3\Core\Eloquent\Collection
	 */
	public function get($columns = ['*'])
	{
		$results = $this->performBuffer($this->db(true))
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
			throw (new ModelNotFoundException('No query results for model [' . get_class($this->model) . ']'));
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

	/**
	 * 主键 条件
	 * 
	 * @param mixed $id
	 * @param array $where
	 * @return $this
	 */
	public function wherePrimaryKey($id, array $where = [])
	{
		return $this->where(array_merge([
			$this->model->getPrimaryKeyName() => $id
		], $where));
	}

	/**
	 * 更新 按 主键
	 * 
	 * @param mixed $id
	 * @param array $data
	 * @param array $filter
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 */
	public function updateByPrimaryKey($id, array $data, array $filter = [])
	{
		$model = $this->wherePrimaryKey($id, $filter)->sole();

		$model->update($data);

		return $model;
	}

	/**
	 * 读取 唯一记录 按 主键
	 * 
	 * @param mixed $id
	 * @param array $filter
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 */
	public function soleByPrimaryKey($id, array $filter = [])
	{
		return $this->wherePrimaryKey($id, $filter)->sole();
	}

	/**
	 * 删除 按 主键
	 * 
	 * @param mixed $id
	 * @param array $filter
	 * @return int
	 */
	public function deleteByPrimaryKey($id, array $filter = []): int
	{
		return $this->wherePrimaryKey($id, $filter)->delete();
	}

	/**
	 * 事务
	 * 
	 * @param \Closure
	 * @return mixed
	 * 
	 * @throws \Throwable
	 */
	public function transaction(Closure $callback)
	{
		static::$transaction = true;

		// 开启事务
		$this->db()->trans_begin();

		try {
			$callbackResult = $callback($this->db());
		}
		catch (Throwable $e) {
			$callbackResult = $e;
		}

		static::$transaction = false;
		$this->resetTransactionDb();

		if ($callbackResult instanceof Throwable) {
			// 回滚事务
			$this->db()->trans_rollback();

			throw $callbackResult;
		}

		// 提交事务
		$this->db()->trans_commit();

		return $callbackResult;
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
		if (in_array($method, $this->ciQbBuffers)) {
			$this->setBuffer($method, $parameters);
			return $this;
		}

		$isRead = in_array($method, $this->ciQbReadPassthru);
		$passthru = array_merge($this->ciQbWritePassthru, $this->ciQbReadPassthru);
		return $this->bufferForwardCallTo($this->db($isRead), $method, $parameters, $passthru);
	}

}
