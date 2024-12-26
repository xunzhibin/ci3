<?php

namespace Xzb\Ci3\Database;

class Connection
{
	/**
	 * 获取 查询构造器 新实例
	 * 
	 * @param string $group
	 * @return \CI_DB_query_builder
	 */
	public function query(string $group = null)
	{
		return $this->reconnect(
			$this->getConfig($group)
		);
	}

	/**
	 * 重新连接
	 * 
	 * @param array $config
	 * @return \CI_DB_query_builder
	 * 
	 * @throws \Xzb\Ci3\Database\ConnectionException
	 */
	protected function reconnect(array $config)
	{
		// 实例化数据库适配器
		$driver = 'CI_DB_' . $config['dbdriver'] . '_driver';
		if (! class_exists($driver)) {
			throw new ConnectionException('Invalid DB driver: ' . $driver);
		}
		$DB = new $driver($config);

		// 检查子驱动程序
		if (! empty($DB->subdriver)) {
			$driver = 'CI_DB_' . $DB->dbdriver . '_' . $DB->subdriver . '_driver';
			if (class_exists($driver)) {
				$DB = new $driver($config);
			}
		}

		$DB->initialize();

		return $DB;
	}

	/**
	 * 获取 配置
	 * 
	 * @param string $group
	 * @return array
	 * 
	 * @throws \Xzb\Ci3\Database\ConnectionException
	 */
	protected function getConfig(string $group = null): array
	{
		$paths = [
			APPPATH . 'config/' . ENVIRONMENT . '/database.php',
			APPPATH.'config/database.php',
		];
		foreach ($paths as $path) {
			if (file_exists($path)) {
				include($path);
				break;
			}
		}

		if (! ($db ?? null)) {
			throw new \ConnectionException('No database connection settings were found in the database config file.');
		}

		$group = $group ?: $active_group ?? null;

		if (! $group) {
			throw new \ConnectionException('You have not specified a database connection group via $active_group in your config/database.php file.');
		}

		if (! array_key_exists($group, $db)) {
			throw new \ConnectionException('You have specified an invalid database connection group ('.$group.') in your config/database.php file.');
		}

		return $db[$group];
	}

}
