<?php
namespace Xzb\Ci3\Core;

use Xzb\Ci3\Http\Exception\{
	HttpExceptionInterface,
	BadRequestException,
	ForbiddenException,
	NotFoundException,
	InternalServerErrorException 
};
use Xzb\Ci3\Core\Http\ValidationException;
use ErrorException;
use Xzb\Ci3\Helpers\Str;

class Exceptions extends \CI_Exceptions
{
	/**
	 * HTTP 状态文本
	 * 
	 * @var array
	 */
	public static $httpStateTexts = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',            // RFC2518
		103 => 'Early Hints',

		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',          // RFC4918
		208 => 'Already Reported',      // RFC5842
		226 => 'IM Used',               // RFC3229

		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',    // RFC7238

		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Content Too Large',                                           // RFC-ietf-httpbis-semantics
		414 => 'URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',                                               // RFC2324
		421 => 'Misdirected Request',                                         // RFC7540
		422 => 'Unprocessable Content',                                       // RFC-ietf-httpbis-semantics
		423 => 'Locked',                                                      // RFC4918
		424 => 'Failed Dependency',                                           // RFC4918
		425 => 'Too Early',                                                   // RFC-ietf-httpbis-replay-04
		426 => 'Upgrade Required',                                            // RFC2817
		428 => 'Precondition Required',                                       // RFC6585
		429 => 'Too Many Requests',                                           // RFC6585
		431 => 'Request Header Fields Too Large',                             // RFC6585
		451 => 'Unavailable For Legal Reasons',                               // RFC7725

		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',                                     // RFC2295
		507 => 'Insufficient Storage',                                        // RFC4918
		508 => 'Loop Detected',                                               // RFC5842
		510 => 'Not Extended',                                                // RFC2774
		511 => 'Network Authentication Required',                             // RFC6585
	];

// ------------------------------- 重构 -------------------------------
	/**
	 * Exception Logger
	 * 
	 * 异常 日志
	 * 
	 * @param int $errno
	 * @param string $errmsg
	 * @param string $errfile
	 * @param int $errline
	 * @return void
	 */
	public function log_exception($errno, $errmsg, $errfile, $errline)
	{
		$errno = isset($this->levels[$errno]) ? $this->levels[$errno] : $errno;

		log_message('error', 'Severity: ' . $errno . ' --> ' . $errmsg . ' in ' . $errfile . ' on line ' . $errline);
	}

	/**
	 * Native PHP error handler
	 * 
	 * 重写 PHP错误处理
	 * CI 辅助函数 system\core\Common.php 文件中 _error_handler 方法调用
	 * 
	 * @param int $severity
	 * @param string $message
	 * @param string $filePath
	 * @param int $line
	 * @return void
	 */
	public function show_php_error($severity, $message, $filePath, $line)
	{
		$this->show_exception(new ErrorException($message, $code = 500, $severity, $filePath, $line));
	}

	/**
	 * 404 Error Handler
	 * 
	 * 重写 404 错误处理
	 * CI 辅助函数 system\core\Common.php 文件中 show_404 方法调用
	 * 
	 * @param string $page
	 * @param bool $logError
	 * @return void
	 */
	public function show_404($page = '', $logError = TRUE)
	{
		$message = 'Not Found: ' . $page;
		// $message = 'Not Found: ' . load_class('URI', 'core')->uri_string();
		// $message .= '. route URI(' . load_class('URI', 'core')->ruri_string() . ')';

		$this->show_error('Not Found', $message, '', 404);
	}

	/**
	 * General Error Page
	 * 
	 * 重写 错误页面显示
	 * CI 辅助函数 system\core\Common.php 文件中 show_error 方法调用
	 * CI 核心类 system\core\Exceptions.php 文件中 show_404 方法调用
	 * CI DB驱动类 system\database\DB_driver.php 文件中 display_error 方法调用(需要在 application\config\database.php 配置文件中 开启 db_debug, 一般生产环境关闭的)
	 * 
	 * @param string $heading
	 * @param string|string[] $message
	 * @param string $template
	 * @param int $statusCode
	 * @return void
	 */
	public function show_error($heading, $message, $template = '', $statusCode = 500)
	{
		if (is_array($message)) {
			$message = implode(' -> ', $message);
		}
		
		$exceptionClasses = [
			400 => BadRequestException::class,
			403 => ForbiddenException::class,
			404 => NotFoundException::class,
			500 => InternalServerErrorException::class,
		];

		$exceptionClass = $exceptionClasses[$statusCode] ?? InternalServerErrorException::class;

		// switch ($statusCode) {
		// 	case 400:
		// 		$exceptionClass = BadRequestException::class;
		// 		break;
		// 	case 403:
		// 		$exceptionClass = ForbiddenException::class;
		// 		break;
		// 	case 404:
		// 		$exceptionClass = NotFoundException::class;
		// 		break;
		// 	case 500:
		// 	default:
		// 		$exceptionClass = InternalServerErrorException::class;
		// 		break;
		// }

		$this->show_exception(new $exceptionClass($message));
	}

	/**
	 * Exception Handler
	 * 
	 * 重写 异常错误处理
	 * CI 辅助函数 system\core\Common.php 文件中 _exception_handler 方法调用
	 * 
	 * @param \Throwable $e
	 * @return void
	 */
	public function show_exception($e)
	{
		// $code = $this->getHttpStatusCode($e);
		$code = $e instanceof HttpExceptionInterface && $httpStatusCode = $e->getHttpStatusCode()
					? $httpStatusCode : 500;

		if ($e instanceof ValidationException) {
			// $message = $e->getErrorBag()->first();
			// $messages = $e->getValidator()->errors()->messages();
			// $message = reset($messages);
			// var_dump($e->getValidator()->errors()->messages());
			$message = $e->getValidator()->errors()->first();
		}
		else {
			// $message = $this->getHttpStateMessage($code);
			$message = self::$httpStateTexts[$httpStatusCode];
		}

		$output = [
			'status' => false,
			'errcode' => $code,
			// 'error' => Str::snake(class_basename($e), ' '),
			'message' => $message,
			'meta' => [
				'timezone' => date_default_timezone_get(),
				'timestamp' => time(),
				'lang' => config_item('language'),
				// 'language' => config_item('language'),
			],
		];

		// 开启 debug
		$output = array_merge($output, [
			'env' => ENVIRONMENT,
			'debug_error' => $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine(),
			'debug_backtrace' => $this->parseDebugBacktrace(debug_backtrace(2)),
		]);

		load_class('Output', 'core')->set_status_header($code)
									->set_content_type('application/json')
									->set_output(json_encode($output, JSON_UNESCAPED_UNICODE))
									->_display();
		exit();
	
        /*
            1. PHP 运行异常 ---> 500
            2. CI框架抛出异常 ---> 500
			3. 数据库操作异常 ---> 500
            4. 模型异常
                未找到 ---> 404
                其它 ---> 500
            5. 业务异常(ServiceException) --> 500
            6. 验证异常(ValidationException) --> 422
                缺少参数 --> 必填
                参数格式错误 --> 非数字、非字母、非字母数字、非手机号、非整形、非数组 等等
                参数值不合法 --> 长度不符合、大小不符合、不在指定值内 等等
            7. 身份验证异常(AuthenticationException) --> 401
            8. 令牌不匹配异常(TokenMismatchException) --> 419
        */
	}

// ------------------------------- 扩展 -------------------------------
	/**
	 * 解析 异常 回溯
	 * 
	 * @param array $backtraces
	 * @return array
	 */
	protected function parseDebugBacktrace(array $backtraces)
	{
		$backtraceData = [];

		foreach ($backtraces as $row) {
			$item = array_intersect_key($row, array_flip(['file', 'line', 'function']));

			$backtraceData[] = $item;
		}

		return $backtraceData;
	}

	// /**
	//  * 获取 响应 HTTP 状态码
	//  * 
	//  * @param \Exception $e
	//  * @return int
	//  */
	// protected function getHttpStatusCode(\Throwable $e)
	// {
	// 	if (
	// 		$e instanceof HttpExceptionInterface
	// 		&& $httpStatusCode = $e->getHttpStatusCode()
	// 	) {
	// 		return $httpStatusCode;
	// 	}

	// 	return 500;
	// }

	// /**
	//  * 获取 响应 HTTP 状态消息
	//  * 
	//  * @param int $httpStatusCode
	//  * @return string
	//  */
	// protected function getHttpStateMessage(int $httpStatusCode)
	// {
	// 	$key = 'http_status_code.' . $httpStatusCode;
	// 	if ($key != ($message = __local($key))) {
	// 		return $message;
	// 	}

	// 	return self::$httpStateTexts[$httpStatusCode];
	// }

}
