<?php
namespace Xzb\Ci3\Core\Http;

use Xzb\Ci3\Http\Exception\NotFoundException;
use Xzb\Ci3\Helpers\Str;

use Xzb\Ci3\Validation\Rules;

abstract class RESTfulController extends \CI_Controller
{
	/**
	 * 表单 请求类 命名空间
	 * 
	 * @var string
	 */
	protected $fromRequestNamespace;

	/**
	 * 分页
	 */
	public function index_get(Request $request)
	{
		$validatedData = $request->getValidatedData();

		return [
			'data' => [
				'title' => '分页',
				'validated_data' => $validatedData
			]
		];
	}

	/**
	 * 筛选
	 */
	public function filter_get(Request $request)
	{
		return [
			'data' => '筛选'
		];
	}

	/**
	 * 详情
	 */
	public function show_get(Request $request)
	{
		return [
			'data' => '详情'
		];
	}

	/**
	 * 存储
	 */
	public function store_post(Request $request)
	{
		return [
			'data' => [
				'api_title' => '存储',
				'validated_data' => $request->getValidatedData(),
			]
		];
	}

	/**
	 * 更新
	 */
	public function update_put(Request $request)
	{
		return [
			'data' => '更新'
		];
	}

	/**
	 * 销毁
	 */
	public function destroy_delete(Request $request)
	{
		return [
			'data' => '销毁'
		];
	}

	/**
	 * 重映射方法
	 * 
	 * @param string $method
	 * @param array $param
	 * @return void
	 */
	public function _remap(string $method, array $params = [])
	{
		if (! method_exists($this, $method)) {
			throw new NotFoundException(
				sprintf('Method [%s::%s] does not exist.', $this->router->directory . static::class, $method)
			);
		}

		$request = $this->requestInstance();
		if ($request instanceof FormRequest) {
			$request->validateResolved();
		}
		array_push($params, $request);

		$resource = call_user_func_array([$this, $method], $params);

		$code = 200;
		$output = [
			'status' => true,
			'errcode' => $code,
			'data' => $resource['data'] ?? $resource,
			'meta' => [
				'timezone' => date_default_timezone_get(),
				'timestamp' => time(),
				'lang' => config_item('language'),
				// 'language' => config_item('language'),
			],
		];

		load_class('Output', 'core')->set_status_header($code)
								->set_content_type('application/json')
								->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * 请求类 实例
	 * 
	 * @var \Xzb\Ci3\Core\Http\Request;
	 */
	protected function requestInstance()
	{
		if ($this->fromRequestNamespace) {
			$requestClassName = Str::upperCamelCase($this->router->method) . 'Request';

			// 空间命名/控制器类名/表单验证类名(方法名 + 后缀)
			$requestClass = $this->fromRequestNamespace . '\\' . $requestClassName;

			if (class_exists($requestClass)) {
				return $requestClass::createInstanceFromPHPGlobals();
			}
		}

		return Request::createInstanceFromPHPGlobals();
	}

}
