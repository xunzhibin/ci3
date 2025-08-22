<?php
namespace Xzb\Ci3\Http\Foundation;

class MessageBag extends ParameterBag
{
	/**
	 * 构造函数
	 * 
	 * @param array $parameters
	 * @return void
	 */
	public function __construct(array $parameters = [])
	{
		$this->replace($parameters);
	}

	/**
	 * 设置
	 * 
	 * @param string $key
	 * @param string $message
	 * @return void
	 */
	public function set(string $key, $message)
	{
		// $this->parameters[$key] = array_unique(array_merge($this->parameters[$key] ?? [], (array)$value));
		if (
			! array_key_exists($key, $this->parameters)
			|| ! in_array($message, $this->parameters[$key])
		) {
			$this->parameters[$key][] = $message;
		}
	}

	/**
	 * 所有
	 * 
	 * @return array
	 */
	public function messages()
	{
		return $this->all();
	}

	/**
	 * 获取 第一条
	 * 
	 * @param string $key
	 * @return string
	 */
	public function first(string $key = null): string
	{
		$messages = is_null($key) ? $this->all() : $this->get($key);

		$firstMessage = reset($messages);

		return is_array($firstMessage) ? reset($firstMessage) : $firstMessage;
	}

	/**
	 * 是否为空
	 * 
	 * @return bool
	 */
	public function isEmpty(): bool
	{
		return ! $this->count() > 0;
	}

// ------------------------------- Countable 接口 -------------------------------
	/**
	 * 统计 个数
	 * 
	 * @return int
	 */
	public function count(): int
	{
		return count($this->parameters, COUNT_RECURSIVE) - count($this->parameters);
	}
}
