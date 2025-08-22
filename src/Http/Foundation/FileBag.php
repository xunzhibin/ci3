<?php
namespace Xzb\Ci3\Http\Foundation;

use Xzb\Ci3\Http\Foundation\File\UploadedFile;
use InvalidArgumentException;

class FileBag extends ParameterBag
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
	 * 获取
	 * 
	 * @param string $key
	 * @param UploadedFile|array|null $default
	 * @return mixed
	 */
	public function get(string $key, $default = null): ?UploadedFile
	{
		if (! is_null($default) && ! is_array($default) && ! $default instanceof UploadedFile) {
			throw new InvalidArgumentException('An uploaded file must be an array or an instance of UploadedFile.');
		}

		return $this->convertToUploadedFileInstance(
			parent::get($key, $default)
		);
	}

	/**
	 * 设置
	 * 
	 * @param string $key
	 * @param UploadedFile|array $value
	 * @return void
	 */
	public function set(string $key, $value)
	{
		if (! is_array($value) && ! $value instanceof UploadedFile) {
			throw new InvalidArgumentException('An uploaded file must be an array or an instance of UploadedFile.');
		}

		parent::set($key, $this->convertToUploadedFileInstance($value));
	}

	/**
	 * 转换为 UploadedFile 实例
	 * 
	 * @param UploadedFile|array
	 * @return UploadedFile|null
	 */
	protected function convertToUploadedFileInstance($file)
	{
		if ($file instanceof UploadedFile) {
			return $file;
		}

		if ($file['error'] == UPLOAD_ERR_NO_FILE) {
			return null;
		}

		return new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
	}


}
