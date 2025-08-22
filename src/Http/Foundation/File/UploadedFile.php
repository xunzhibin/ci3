<?php
namespace Xzb\Ci3\Http\Foundation\File;

class UploadedFile extends File
{
	/**
	 * 客户端 名称
	 * 
	 * @var string
	 */
	private $clientName;

	/**
	 * 客户端 mime类型
	 * 
	 * @var string
	 */
	private $clientMimeType;

	/**
	 * 客户端 大小
	 * 
	 * @var int
	 */
	private $clientSize;

	/**
	 * 上传 错误代码
	 * 
	 * @var int
	 */
	private $uploadError;

	/**
	 * 构造函数
	 * 
	 * @param string $path
	 * @param string $name
	 * @param string $mimeType
	 * @param string $error
	 * @return void
	 */
	public function __construct(string $path, string $name, string $mimeType = null, int $size = null, int $error = null)
	{
		$this->clientName = $this->getName($name);
		$this->clientMimeType = $mimeType ?: 'application/octet-stream';
		$this->clientSize = $size;
		$this->uploadError = $error ?: UPLOAD_ERR_OK;

		parent::__construct($path, UPLOAD_ERR_OK === $this->uploadError);
	}

	/**
	 * 获取 客户端 文件名
	 * 
	 * @return string
	 */
	public function getClientFilename(): string
	{
		return $this->clientName;
	}

	/**
	 * 获取 客户端 扩展名
	 * 
	 * @return string
	 */
	public function getClientExtension(): string
	{
		return pathinfo($this->getClientFilename(), PATHINFO_EXTENSION);
	}

	/**
	 * 获取 客户端 文件 基本名称
	 * 
	 * @param string $suffix
	 * @return string
	 */
	public function getClientBasename(string $suffix = ''): string
	{
		$filename = $this->getClientFilename();

		if ($suffix === '.' . $this->getClientExtension()) {
			return pathinfo($filename, PATHINFO_FILENAME);
		}

		return $filename;
	}

	/**
	 * 获取 客户端 mime类型
	 * 
	 * @return string
	 */
	public function getClientMimeType(): string
	{
		return $this->clientMimeType;
	}

	/**
	 * 获取 客户端 大小
	 * 
	 * @return int
	 */
	public function getClientSize(): int
	{
		return $this->clientSize;
	}

	/**
	 * 获取 上传错误代码
	 * 
	 * @return int
	 */
	public function getUploadError(): int
	{
		return $this->uploadError;
	}

	/**
	 * 是否为 有效上传文件
	 * 
	 * @return bool
	 */
	public function isValid(): bool
	{
		if (UPLOAD_ERR_OK === $this->error && is_uploaded_file($this->getPathname())) {
			return true;
		}

		return false;
	}

	/**
	 * 移动
	 * 
	 * @param string $dir
	 * @param string $name
	 * @return File
	 */
	public function move(string $dir, string $name): File
	{
		if ($this->isValid()) {
			$targetFile = $this->getTargetFileInstance($dir, $name);

			set_error_handler(function ($type, $msg) use (&$error) { $error = $msg; });
			try {
				$moved = move_uploaded_file($this->getPathname(), $targetFile);
			}
			finally {
				restore_error_handler();
			}

			if (! $moved) {
				throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s).', $this->getPathname(), $targetFile, strip_tags($error)));
			}

			@chmod($targetFile, 0666 & ~umask());

			return $targetFile;
		}

		throw new FileException($this->getUploadErrorMessage());
	}

	/**
	 * 获取 上传错误信息
	 * 
	 * @return string
	 */
	public function getUploadErrorMessage(): string
	{
		static $errors = [
			UPLOAD_ERR_INI_SIZE => 'The file "%s" exceeds your upload_max_filesize ini directive (limit is %d KiB).',
			UPLOAD_ERR_FORM_SIZE => 'The file "%s" exceeds the upload limit defined in your form.',
			UPLOAD_ERR_PARTIAL => 'The file "%s" was only partially uploaded.',
			UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
			UPLOAD_ERR_CANT_WRITE => 'The file "%s" could not be written on disk.',
			UPLOAD_ERR_NO_TMP_DIR => 'File could not be uploaded: missing temporary directory.',
			UPLOAD_ERR_EXTENSION => 'File upload was stopped by a PHP extension.',
		];

		$message = $errors[$this->uploadError] ?? 'The file "%s" was not uploaded due to an unknown error.';
		$maxFileSize = UPLOAD_ERR_INI_SIZE === $this->uploadError ? self::getUploadMaxFilesize() / 1024 : 0;

		return sprintf($message, $this->getClientName(), $maxFilesize);
	}

	/**
	 * 获取 上传文件大小 最大限制(字节)
	 * 
	 * @return int|float
	 */
	public static function getUploadMaxFilesize()
	{
		$postMaxSize = self::parseFilesize(ini_get('post_max_size'));
		$uploadMaxFilesize = self::parseFilesize(ini_get('upload_max_filesize'));

		return min($postMaxSize ?: PHP_INT_MAX, $uploadMaxFilesize ?: PHP_INT_MAX);
	}

	/**
	 * 解析 文件大小(字节)
	 * 
	 * @param string $size
	 * @return int|float
	 */
	private static function parseFilesize(string $size)
	{
		if (! $size) {
			return 0;
		}

		$size = strtolower($size);

		$max = ltrim($size, '+');
		// if (substr($max, 0, strlen($needle = '0x')) === $needle) {
		// 	$max = intval($max, 16);
		// }
		// else if (substr($max, 0, strlen($needle = '0')) === $needle) {
		// 	$max = intval($max, 8);
		// }
		// else {
		// 	$max = (int)$max;
		// }
		$max = intval($max, 0);

		switch (substr($size, -1)) {
			case 't': $max *= 1024;
			case 'g': $max *= 1024;
			case 'm': $max *= 1024;
			case 'k': $max *= 1024;
		}

		return $max;
	}

}
