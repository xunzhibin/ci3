<?php
namespace Xzb\Ci3\Http\Foundation;

use IteratorAggregate;
use ArrayIterator;
use Countable;

class HeaderBag extends ParameterBag
{
	/**
	 * 大写
	 * 
	 * @var string
	 */
	protected const UPPER = '_ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * 小写
	 * 
	 * @var string
	 */
	protected const LOWER = '-abcdefghijklmnopqrstuvwxyz';

	/**
	 * 构造函数
	 * 
	 * @param array $parameters
	 * @return void
	 */
	public function __construct(array $parameters = [])
	{
		foreach ($parameters as $key => $value) {
			$this->set($key, $value);
		}
	}

	/**
	 * 获取
	 * 
	 * @param string $key
	 * @param string|null $default
	 * @return string|null
	 */
	public function get(string $key, $default = null)
	{
		return parent::get($this->keyToLower($key), $default);
	}

	/**
	 * 设置
	 * 
	 * @param string $key
	 * @param string|null $value
	 * @return void
	 */
	public function set(string $key, $value)
	{
		return parent::set($this->keyToLower($key), $value);
	}

	/**
	 * 删除
	 * 
	 * @param string $key
	 * @return void
	 */
	public function remove(string $key)
	{
		return parent::remove($this->keyToLower($key));
	}

	/**
	 * 是否存在
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function has(string $key): bool
	{
		return parent::has($this->keyToLower($key));
	}

	/**
	 * 键 转 小写
	 * 
	 * @param string $key
	 * @return string
	 */
	protected function keyToLower(string $key): string
	{
		return strtr($key, self::UPPER, self::LOWER);
	}

}
