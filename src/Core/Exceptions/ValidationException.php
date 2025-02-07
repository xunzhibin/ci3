<?php

namespace Xzb\Ci3\Core\Exceptions;

use Exception;

class ValidationException extends Exception
{
	/**
	 * 消息 集合
	 * 
	 * @var array
	 */
	protected $messages = [];

	/**
	 * 设置 消息集合
	 * 
	 * @param array $messages
	 * @return $this
	 */
	public function setMessages(array $messages)
	{
		$this->messages = $messages;

		return $this;
	}

	/**
	 * 获取 消息集合
	 * 
	 * @return array
	 */
	public function getMessages()
	{
		return $this->messages;
	}

}
