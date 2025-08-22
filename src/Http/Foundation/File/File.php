<?php
namespace Xzb\Ci3\Http\Foundation\File;

use SplFileInfo;

class File extends SplFileInfo
{
	/**
	 * 构造函数
	 * 
	 * @param string $path
	 * @param string $isCheckPath
	 */
	public function __construct(string $path, bool $isCheckPath = true)
	{
		if ($isCheckPath && ! is_file($path)) {
			throw new FileException(sprintf('The file "%s" does not exist', $path));
		}

		parent::__construct($path);
	}

	/**
	 * 获取 内容
	 * 
	 * @return string
	 */
	public function getContent(): string
	{
		if (false === $content = file_get_contents($this->getPathname())) {
			throw new FileException(sprintf('Could not get the content of the file "%s".', $this->getPathname()));
		}

		return $content;
	}

	/**
	 * 移动
	 * 
	 * @param string $directory
	 * @param string $name
	 * @return self
	 */
	public function move(string $directory, string $name): self
	{
		$target = $this->getTargetFileInstance($directory, $name);

		set_error_handler(function ($type, $msg) use (&$error) { $error = $msg; });
		try {
			$moved = rename($this->getPathname(), $target);
		}
		finally {
			restore_error_handler();
		}

		if (! $moved ) {
			throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s).', $this->getPathname(), $target, strip_tags($error)));
		}

		@chmod($target, 0666 & ~umask());

		return $target;
	}

	/**
	 * 获取 名称
	 *
	 * @param string $name
	 * @return string
	 */
	protected function getName(string $name): string
	{
		$name = str_replace('\\', '/', $name);

		if (false === $pos = strrpos($name, '/')) {
			return $name;
		}

		return substr($name, $pos + 1);
	}

	/**
	 * 获取 目标文件 实例
	 * 
	 * @param string $directory
	 * @param string $name
	 * @return File
	 */
	protected function getTargetFileInstance(string $directory, string $name): self
	{
		if (! is_dir($directory)) {
			if (false === @mkdir($directory, 0777, true) && ! is_dir($directory)) {
				throw new FileException(sprintf('Unable to create the "%s" directory.', $directory));
			}
		}
		else if (! is_writable($directory)) {
			throw new FileException(sprintf('Unable to write in the "%s" directory.', $directory));
		}

		$targetFile = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $this->getName($name);

		return new self($targetFile);
	}

}
