<?php

namespace Xzb\Ci3\Core\Exceptions;

use Xzb\Ci3\Core\Exceptions\Http\{
	HttpException,
	NotFoundException,
	UnprocessableEntityException,
	InternalServerErrorException 
};
use Xzb\Ci3\Core\Eloquent\{
	ModelNotFoundException
};
use Xzb\Ci3\Helpers\Str;
use Throwable;
use ErrorException;

class Exceptions extends \CI_Exceptions
{
	/**
	 * HTTP 状态码
	 * 
	 * @var int
	 */
	protected $httpStatusCode;

// ------------------------------- 重构 -------------------------------
	/**
	 * 404 Error Handler
	 * 
	 * 重写 404 错误处理
	 * CI 辅助函数 system\core\Common.php 文件中 show_404 方法调用
	 * 
	 * @param string $page
	 * @param bool $logError
	 * @return void
	 * 
	 * @throws \Xzb\Ci3\Core\Exceptions\Http\NotFoundException
	 */
	public function show_404($page = '', $logError = TRUE)
	{
		throw new NotFoundException(
			'Not Found: ' . $page
		);
	}

	/**
	 * General Error Page
	 * 
	 * 重写 错误页面显示
	 * CI 辅助函数 system\core\Common.php 文件中 show_error 方法调用
	 * CI 核心类 system\core\Exceptions.php 文件中 show_404 方法调用(当前父类已重写, 不在调用)
	 * CI DB驱动类 system\database\DB_driver.php 文件中 display_error 方法调用(需要在 application\config\database.php 配置文件中 开启 db_debug, 一般生产环境关闭的)
	 * 
	 * @param string $heading
	 * @param string|string[] $message
	 * @param string $template
	 * @param int $statusCode
	 * @return void
	 * 
	 * @throws \Xzb\Ci3\Core\Exceptions\Http\InternalServerErrorException
	 */
	public function show_error($heading, $message, $template = '', $statusCode = 500)
	{
		if (is_array($message)) {
			$message = implode(' -> ', $message);
		}
		// $message = $heading . ' -> ' . $message;

		throw new InternalServerErrorException($message);
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
	 * @param int $statusCode
	 * @return void
	 * 
	 * @throws \ErrorException
	 */
	public function show_php_error($severity, $message, $filePath, $line, $statusCode = 500)
	{
		// 抛出 错误异常
		throw new ErrorException($message, $code = 500, $severity, $filePath, $line);
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
		$e = $this->prepareException($e);

		load_class('Output', 'core')->response($e->toResponse(), $this->httpStatusCode ?: $e->getHttpStatusCode());
	
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
	 * 准备渲染异常
	 * 
	 * @param \Throwable $e
	 * @return \Throwable
	 */
	protected function prepareException(Throwable $e)
	{
		if ($e instanceof HttpException) {
			return $e;
		}
		else if ($e instanceof ModelNotFoundException) {
			return new NotFoundException($e->getMessage(), $e->getCode(), $e);
		}
		else if ($e instanceof ValidationException) {
			return $this->convertValidationExceptionToResponse($e);
		}

		return new InternalServerErrorException($e->getMessage(), $e->getCode(), $e);
	}

	/**
	 * 验证异常 转为 响应异常
	 * 
	 * @param \Xzb\Ci3\Core\Exceptions\ValidationException
	 * @return \Xzb\Ci3\Core\Exceptions\Http\UnprocessableEntityException
	 */
	protected function convertValidationExceptionToResponse(ValidationException $e)
	{
		$messages = $e->getMessages();
		$message = reset($messages);

		$httpException = new UnprocessableEntityException($e->getMessage(), $e->getCode(), $e);

		return $httpException->setErrorMessage($message);
	}

}
