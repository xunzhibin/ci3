<?php
namespace Xzb\Ci3\Http\Foundation;

use ArrayAccess;
use Xzb\Ci3\Core\HttpFoundation\File\UploadedFile;

class Request implements ArrayAccess
{
	/**
	 * 查询($_GET) 参数
	 * 
	 * @var InputBag
	 */
	public $query;

	/**
	 * 请求($_POST) 参数
	 * 
	 * @var InputBag
	 */
	public $request;

	/**
	 * 上传 文件($_FILES)
	 * 
	 * @var FileBag
	 */
	public $files;

	/**
	 * 服务器和环境($_SERVER) 参数
	 * 
	 * @var ServerBag
	 */
	public $server;

	/**
	 * 请求头 参数
	 * 
	 * @var HeaderBag
	 */
	public $headers;

	/**
	 * 请求 内容
	 * 
	 * @var string
	 */
	protected $content;

	/**
	 * 请求 方法
	 * 
	 * @var string
	 */
	protected $method;

	/**
	 * 构造函数
	 * 
	 * @param array $query
	 * @param array $request
	 * @param array $server
	 */
	public function __construct(array $query = [], array $request = [], array $files = [], array $server = [])
	{
		$this->query = new InputBag($query);
		$this->request = new InputBag($request);
		$this->files = new FileBag($files);
		$this->server = new ServerBag($server);
		$this->headers = new HeaderBag($this->server->getHeaders());

		$this->content = null;
		$this->method = null;
	}

	/**
	 * 检索 输入项
	 * 
	 * @param string|null $key
	 * @param string|int|float|bool|array|null $default
	 * @return string|int|float|bool|array|null
	 */
	public function query(string $key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->query->all();
		}

		return $this->query->get($key, $default);
	}

	// /**
	//  * 检索 输入项
	//  * 
	//  * @param string|null $key
	//  * @param string|int|float|bool|array|null $default
	//  * @return string|int|float|bool|array|null
	//  */
	// public function request(string $key = null, $default = null)
	// {
	// 	if (is_null($key)) {
	// 		return $this->request->all();
	// 	}

	// 	return $this->request->get($key, $default);
	// }

	/**
	 * 检索 文件
	 * 
	 * @param string|null $key
	 * @param UploadedFile|array|null $default
	 * @return UploadedFile|array|null
	 */
	public function file(string $key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->files->all();
		}

		return $this->files->get($key, $default);
	}

	/**
	 * 检索 服务器
	 * 
	 * @param string|null $key
	 * @param string|array|null $default
	 * @return string|array|null
	 */
	public function server(string $key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->server->all();
		}

		return $this->server->get($key, $default);
	}

	/**
	 * 检索 请求头
	 * 
	 * @param string|null $key
	 * @param string|array|null $default
	 * @return string|array|null
	 */
	public function header(string $key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->headers->all();
		}

		return $this->headers->get($key, $default);
	}

	/**
	 * 获取 请求内容
	 * 
	 * @return string
	 */
	public function getContent(): string
	{
		if (null === $this->content || false === $this->content) {
			$this->content = file_get_contents('php://input');
		}

		return $this->content;
	}

	/**
	 * 获取 请求方法
	 * 
	 * @return string
	 */
	public function getMethod(): string
	{
		if ($this->method) {
			return $this->method;
		}

		return $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
	}

	/**
	 * 获取 输入源
	 * 
	 * @return \Xzb\Ci3\Http\Foundation\ParameterBag
	 */
	protected function getInputSource()
	{
		return in_array($this->getMethod(), ['GET', 'HEAD']) ? $this->query : $this->request;
	}

// -----------------------------------------------------------------------------------
	/**
	 * 检索 输入项 根据 请求方法
	 * 
	 * @param string|null $key
	 * @param string|int|float|bool|array|null $default
	 * @return string|int|float|bool|array|null
	 */
	public function input(string $key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->getInputSource()->all();
		}

		return $this->getInputSource()->get($key, $default);
	}

	/**
	 * 检索 输入项
	 * 
	 * @param string|string[] $keys
	 * @return array
	 */
	public function all($keys = null): array
	{
		$input = array_replace_recursive(
			$this->request->all() + $this->query->all(),
			$this->files->all()
		);

		if (! $keys) {
			return $input;
		}

		$results = [];

		foreach (is_array($keys) ? $keys : func_get_args() as $key) {
			$results[$key] = $input[$key] ?? null;
		}

		return $results;
	}

	/**
	 * 获取 请求方法
	 * 
	 * @return string
	 */
	public function method()
	{
		return $this->getMethod();
	}

	/**
	 * 检查 是否为 指定类型 请求方法
	 * 
	 * @param string $method
	 * @return bool
	 */
	public function isMethod(string $method): bool
	{
		return $this->getMethod() === strtoupper($method);
	}

	/**
	 * 创建 新实例 
	 * 
	 * @return static
	 */
	public static function createInstanceFromPHPGlobals(): self
	{
		$request = new static($_GET, $_POST, $_FILES, $_SERVER);

		if (
			strncmp($request->headers->get('content_type', ''), $starts = 'application/x-www-form-urlencoded', strlen($starts)) === 0
			&& in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), ['PUT', 'DELETE', 'PATCH'])
		) {
			$content = $request->getContent();

			$data = json_decode($content, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				parse_str($content, $data);
			}

			$request->request = new InputBag($data);
		}

		return $request;
	}

// ------------------------------- ArrayAccess(数组式访问) 接口 -------------------------------
	/**
	 * 是否存在
	 * 
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists($offset): bool
	{
		return array_key_exists($offset, $this->all());

	}

	/**
	 * 获取
	 * 
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	/**
	 * 设置
	 * 
	 * @param string $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->getInputSource()->set($offset, $value);
	}

	/**
	 * 删除
	 * 
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		$this->getInputSource->remove($offset);
	}

// ------------------------------- 魔术方法 -------------------------------
	/**
	 * 动态 获取 属性
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function __get(string $key)
	{
		return $this->all()[$key] ?? null;
	}

	/**
	 * 属性 是否存在
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function __isset(string $key): bool
	{
		return ! is_null($this->__get($key));
	}

}
