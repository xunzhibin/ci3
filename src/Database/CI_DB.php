<?php

use Xzb\Ci3\Database\DatabaseFailedException;

class CI_DB extends CI_DB_query_builder
{

// ---------------------------------- INSERT ----------------------------------
	/**
	 * 批 插入
	 * 
	 * @param string|array $table
	 * @param array $values
	 * @param bool $escape
	 * @param int $batchSize
	 * @return int
	 */
	public function insert_batch($table = null, $values = null, $escape = null, $batchSize = 100): int
	{
		if (is_array($table)) {
			$values = $table;
			$table = null;
		}

		// 转为 二维数组
		if ($values && is_array($values) && ! is_array(reset($values))) {
			$values = [$values];
		}

		$insertRows = parent::insert_batch($table, $values, $escape, $batchSize);
		if ($insertRows === false) {
			$this->throwDatabaseFailedException('Insert SQL error');
		}

		return $insertRows;
	}

	/**
	 * 插入
	 * 
	 * @param string|array
	 * @param array $value
	 * @param bool $escape
	 * @return int
	 */
	public function insert($table = '', $value = NULL, $escape = NULL): int
	{
		if (is_array($table)) {
			$value = $table;
			$table = '';
		}

		return $this->insert_batch($table, $value, $escape);
	}

	/**
	 * 插入 并 获取主键值
	 * 
	 * @param array $value
	 * @return int
	 */
	public function insertGetId(array $value): int
	{
		$this->insert($value);

		return $this->insert_id();
	}

// ---------------------------------- UPDATE ----------------------------------
	/**
	 * 更新
	 * 
	 * @param string|array $table
	 * @param array $data
	 * @param mixed $where
	 * @param int $limit
	 * @return int
	 */
	public function update($table = '', $data = NULL, $where = NULL, $limit = NULL): int
	{
		if (is_array($table)) {
			$data = $table;
			$table = '';
		}

		if (parent::update($table, $data, $where, $limit) === false) {
			$this->throwDatabaseFailedException('Update SQL error');
		}

		return $this->affected_rows();
	}

// ---------------------------------- DELETE ----------------------------------
	/**
	 * 删除
	 * 
	 * @param string|array
	 * @param mixed $where
	 * @param mixed $limit
	 * @param bool $resetData
	 * @return int
	 */
	public function delete($table = '', $where = '', $limit = NULL, $resetData = TRUE): int
	{
		if (parent::delete($table, $where, $limit, $resetData) === false) {
			$this->throwDatabaseFailedException('Delete SQL error');
		}

		return $this->affected_rows();
	}

// ---------------------------------- SELECT ----------------------------------
	/**
	 * 读取
	 * 
	 * @param string|array $table
	 * @param int $limit
	 * @param int $offset
	 * @return \CI_DB_result
	 */
	public function get($table = '', $limit = NULL, $offset = NULL)
	{
		if (is_array($table)) {
			if (! $this->qb_select) {
				$this->select($table);
			}

			if ($table != ['*']) {
				$this->select($table);
			}

			$table = '';
		}

		$results = parent::get($table, $limit, $offset);
		if ($results === false) {
			$this->throwDatabaseFailedException('Query SQL error');
		}

		return $results;
	}

	/**
	 * 总数
	 * 
	 * @return int
	 */
	public function count()
	{
		return parent::count_all_results();
	}

	/**
	 * MAX
	 * 
	 * @param string $column
	 * @param string $alias
	 * @return string
	 */
	public function max(string $column, $alias = ''): string
	{
		if (! $alias) {
			$alias = 'max_' . $column;
		}

		return $this->select_max($column, $alias)->get()->row()->{$alias};
	}

// ---------------------------------- FROM ----------------------------------
	/**
	 * FROM
	 * 
	 * @param mixed $from
	 * @return \CI_DB_query_builder
	 */
	public function from($from)
	{
		if ($this->qb_from) {
			return $this;
		}

		return parent::from($from);
	}

// ---------------------------------- WHERE ----------------------------------
	/**
	 * WHERE
	 * 
	 * @param mixed $key
	 * @param mixed $value
	 * @param bool $escape
	 * @return \CI_DB_query_builder
	 */
	public function where($key, $value = NULL, $escape = NULL)
	{
		if (! is_array($key)) {
			$key = [$key => $value];
		}

		foreach ($key as $k => $v) {
			if (is_array($v)) {
				parent::where_in($k, $v, $escape);
			}
			else {
				parent::where($k, $v, $escape);
			}
		}

		return $this;
	}

// ---------------------------------- LIKE ----------------------------------
	/**
	 * LIKE GROUP
	 * 
	 * AND (LIKE OR LIKE)
	 * 
	 * @param array|string $columns
	 * @param string $value
	 * @param string $side
	 * @param bool $escape
	 * @return \CI_DB_query_builder
	 */
	public function likeGroup($columns, string $value = null, string $side = 'both', bool $escape = NULL)
	{
		if (! is_array($columns)) {
			$columns = [$columns => $value];
		}

		// 条件组 开始
		count($columns) > 1 && $this->group_start();

		$isFirst = true;
		foreach ($columns as $column => $keyword) {
			if (is_numeric($column)) {
				$column = $keyword;
				$keyword = $value;
			}

			// 第一个
			if ($isFirst) {
				$this->like($column, $keyword, $side, $escape);
				$isFirst = false;
				continue;
			}

			$this->or_like($column, $keyword, $side, $escape);
		}

		// 条件组 结束
		count($columns) > 1 && $this->group_end();

		return $this;
	}

// ---------------------------------- ORDER BY ----------------------------------
	/**
	 * ORDER BY
	 * 
	 * @param mixed $columns
	 * @param string $direction
	 * @param bool $escape
	 * @return \CI_DB_query_builder
	 */
	public function orderBy($orderby, string $direction = 'asc', bool $escape = NULL)
	{
		if (is_string($orderby)) {
			return $this->order_by($orderby, $direction, $escape);
		}

		foreach ($orderby as $column => $value) {
			$column = is_numeric($column) ? $value : $column;
			$direction = is_numeric($column) ? $direction : $value;

			$this->order_by($column, $direction, $escape);
		}

		return $this;
	}


// ---------------------------------- 分页 ----------------------------------
	/**
	 * 偏移量 分页
	 * 
	 * @param int $page
	 * @param int $perPage
	 * @return \CI_DB_query_builder
	 */
	public function forPage(int $page, int $perPage)
	{
		return $this->limit($perPage)->offset(($page - 1) * $perPage);
	}

// ---------------------------------- 异常 ----------------------------------
	/**
	 * 抛出 数据库失败 异常
	 * 
	 * @param string $message
	 * 
	 * @throws \Xzb\CI3\Database\DatabaseFailedException
	 */
	protected function throwDatabaseFailedException(string $message = '')
	{
		$message = $message ?: 'SQL error';

		if ($this->error()['code']) {
			$message .= '(' . $this->error()['code'] . ')'; 
		}
		if ($this->error()['message']) {
			$message .= ': ' . $this->error()['message'];
		}

		throw new DatabaseFailedException($message);
	}

// ---------------------------------- 魔术方法 ----------------------------------
	// 析构函数
	public function __destruct()
	{
        load_class('Config', 'core')->set_item(
            'db_queries',
            array_merge(config_item('db_queries') ?? [], $this->queries)
        );
	}


}
