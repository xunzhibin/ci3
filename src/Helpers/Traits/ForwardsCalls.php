<?php

namespace Xzb\Ci3\Helpers\Traits;

use BadMethodCallException;
use Error;

trait ForwardsCalls
{
	/**
	 * 将方法调用转发给指定对象
	 * 
	 * @param mixed $object
	 * @param string $method
	 * @param array $parameters
	 * @return mixed
	 * 
	 * @throws \BadMethodCallException
	 */
	protected function forwardCallTo($object, string $method, array $parameters)
	{
		try {
			return $object->{$method}(...$parameters);
		}
		catch (Error|BadMethodCallException $e) {
			$pattern = '~^Call to undefined method (?P<class>[^:]+)::(?P<method>[^\(]+)\(\)$~';
			if (! preg_match($pattern, $e->getMessage(), $matches)) {
				throw $e;
			}

			if ($matches['class'] != get_class($object) || $matches['method'] != $method) {
				throw $e;
			}

			throw new BadMethodCallException(
				sprintf('Call to undefined method %s::%s()', static::class, $method)
			);
		}
	}

	/**
	 * 缓冲器
	 * 
	 * @var array
	 */
	protected $buffers = [];

	/**
	 * 设置 缓冲器
	 * 
	 * @param string $method
	 * @param array $parameters
	 * @return $this
	 */
	protected function setBuffer(string $method, array $parameters)
	{
		array_push($this->buffers, compact('method', 'parameters'));

		return $this;
	}

	/**
	 * 重置 缓冲器
	 * 
	 * @return $this
	 */
	protected function resetBuffer()
	{
		$this->buffers = [];

		return $this;
	}

	/**
	 * 执行 Eloquent Model 类方法
	 * 
	 * @param mixed $object
	 * @return mixed $object
	 */
	protected function performBuffer($object)
	{
		foreach ($this->buffers as $value) {
			extract($value);
			$object = $object->{$method}(...$parameters);
		}

		return $object;
	}

	/**
	 * 将方法调用转发给指定对象
	 * 
	 * @param mixed $object
	 * @param string $method
	 * @param array $parameters
	 * @return mixed
	 */
	protected function bufferForwardCallTo($object, string $method, array $parameters, array $passthru = [])
	{
		if (in_array($method, $passthru)) {
			return $this->forwardCallTo($this->performBuffer($object), $method, $parameters);
		}

		throw new \BadMethodCallException(
			sprintf('Call to undefined method %s::%s()', static::class, $method)
		);
	}

}
