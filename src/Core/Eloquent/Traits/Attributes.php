<?php

namespace Xzb\Ci3\Core\Eloquent\Traits;

use Xzb\Ci3\Helpers\{
	Str,
	Transform,
	Date
};

trait Attributes
{
	/**
	 * 属性
	 * 
	 * @var array
	 */
	protected $attributes = [
		// 属性 => 值
	];

	/**
	 * 属性 原始值
	 * 
	 * @var array
	 */
	protected $original = [
		// 属性 => 值
	];

	/**
	 * 更改属性
	 * 
	 * @var array
	 */
	protected $changes = [];

	/**
	 * 强制转换 属性
	 * 
	 * @var array
	 */
	protected $casts = [
		// 属性key => 类型
	];

	/**
	 * 序列化 隐藏属性
	 * 
	 * @var array
	 */
	protected $hidden = [];

	/**
	 * 访问器属性 缓存
	 * 
	 * @var array
	 */
	protected static $accessorAttributeCache = [];

	/**
	 * 附加 属性
	 * 
	 * @var array
	 */
	protected $appends = [];

	/**
	 * 设置 属性
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function setAttribute(string $key, $value)
	{
		// 属性 修改器
		if ($this->hasSetMutator($key)) {
			return $this->setMutatedAttributeValue($key, $value);
		}

		$this->setRawAttribute($key, $value);

		return $this;
	}

	/**
	 * 获取 指定属性
	 * 
	 * @param string $key
	 * @return mixed
	 * 
	 * @throws \Xzb\Ci3\Database\Eloquent\ModelMissingAttributeException
	 */
	public function getAttribute(string $key)
	{
		if (strlen($key)) {
			if (
				array_key_exists($key, $this->attributes)
				|| array_key_exists($key, $this->casts)
				|| $this->hasGetAccessor($key)
			) {
				return $this->getAttributeValue($key);
			}
		}

		$message = 'The attribute [' . $key . '] either does not exist or was not retrieved for model [' . static::class . ']';
		throw (new ModelMissingAttributeException($message));
	}

	/**
	 * 获取 属性值
	 * 
	 * @param string $key
	 * @return mixed
	 */
	protected function getAttributeValue(string $key)
	{
		$value = $this->getAttributes()[$key] ?? null;

		// 属性 访问器
		if ($this->hasGetAccessor($key)) {
			return $this->getAccessorAttributeValue($key, $value);
		}

		// 属性 强制转换
		if ($this->isCast($key)) {
			return $this->castAttributeValue($key, $value);
		}

		return $value;
	}

	/**
	 * 获取 属性
	 * 
	 * @return array
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}

	/**
	 * 设置 原始属性
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function setRawAttribute(string $key, $value)
	{
		$this->attributes[$key] = $this->transformDatabaseValue($key, $value);

		return $this;
	}

	/**
	 * 设置 原始属性
	 * 
	 * @param array $attributes
	 * @param bool $sync
	 * @return $this
	 */
	public function setRawAttributes(array $attributes, bool $sync = false)
	{
		foreach ($attributes as $key => $value) {
			$this->setRawAttribute($key, $value);
		}

		if ($sync) {
			$this->syncOriginal();
		}

		return $this;
	}

	/**
	 * 获取 属性
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function getRawAttribute(string $key)
	{
		return $this->attributes[$key];
	}

	/**
	 * 获取 插入 属性
	 * 
	 * @return array
	 */
	public function getAttributesForInsert(): array
	{
		// 更新 操作时间
		$this->updateTimestamps();

		return array_intersect_key($this->getAttributes(), $this->getColumns());
	}

	/**
	 * 获取 更新 属性
	 * 
	 * @return array
	 */
	public function getAttributesForUpdate(): array
	{
		// 更新操作时间
		$this->updateTimestamps();

		return array_intersect_key($this->getEdited(), $this->getColumns());
	}

	/**
	 * 获取 可序列化 属性
	 * 
	 * @return array
	 */
	protected function getArrayableAttributes(): array
	{
		$attributes = $this->getAttributes();

		if (count($this->getHidden()) > 0) {
			$attributes = array_diff_key($attributes, array_flip($this->getHidden()));
		}

		return $attributes;
	}

	/**
	 * 转换 数据库 值
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	protected function transformDatabaseValue(string $key, $value)
	{
		if (
			$this->hasColumn($key)
			&& (! is_null($value) || ! $this->isColumnNullable($key))
		) {
			return Transform::DBDataType($this->getColumnType($key), $value);
		}

		return $value;
	}

	/**
	 * 是否有 属性 修改器
	 * 
	 * @param string $key
	 * @return bool
	 */
	protected function hasSetMutator(string $key): bool
	{
		return method_exists($this, 'set' . Str::upperCamel($key) . 'Attribute');
	}

	/**
	 * 设置 修改器 属性值
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	protected function setMutatedAttributeValue(string $key, $value)
	{
		$value = $this->{'set' . Str::upperCamel($key) . 'Attribute'}($value);

		if (! is_array($value)) {
			$value = [$key => $value];
		}
	
		foreach ($value as $k => $v) {
			$this->setRawAttribute($k, $v);
		}

		return $this;
	}

	/**
	 * 是否存在 属性 访问器
	 * 
	 * @param string $Key
	 * @return bool
	 */
	protected function hasGetAccessor(string $key): bool
	{
		return method_exists($this, 'get' . Str::upperCamel($key) . 'Attribute');
	}

	/**
	 * 获取 属性访问器 属性值
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	protected function getAccessorAttributeValue(string $key, $value)
	{
		return $this->{'get' . Str::upperCamel($key) . 'Attribute'}($value);
	}

	/**
	 * 获取 访问器 属性
	 * 
	 * @return array
	 */
	public function getAccessorAttributes(): array
	{
		if (! isset(static::$accessorAttributeCache[static::class])) {
			static::cacheAccessorAttributes($this);
		}

		return static::$accessorAttributeCache[static::class];
	}

	/**
	 * 缓存 访问器 属性
	 * 
	 * @param object|string $class
	 * @return void
	 */
	public static function cacheAccessorAttributes($class)
	{
		// 获取类名
		$className = (new \ReflectionClass($class))->getName();

		// 获取 类 所有方法
		$methods = implode(';', get_class_methods($className));

		// 匹配 访问器 方法
		preg_match_all('/(?<=^|;)get([^;]+?)Attribute(;|$)/', $methods, $matches);

		// 缓存
		static::$accessorAttributeCache[$className] = array_map(function ($value) {
			return Str::snake($value);
		}, $matches[1]);
	}

	/**
	 * 同步 属性 原始值
	 * 
	 * @return $this
	 */
	public function syncOriginal()
	{
		$this->original = $this->getAttributes();

		return $this;
	}

	/**
	 * 获取 属性 原始值
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function getRawOriginal(string $key)
	{
		return $this->original[$key];
	}

	/**
	 * 获取 被编辑的 属性
	 * 
	 * @return array
	 */
	public function getEdited(): array
	{
		$edited = [];

		foreach ($this->getAttributes() as $key => $value) {
			if (
				// 不存在 原始值
				! array_key_exists($key, $this->original)
				// 属性 当前值 和 原始值 不恒等
				|| $this->attributes[$key] !== $this->original[$key]
			) {
				$edited[$key] = $value;
			}
		}

		return $edited;
	}

	/**
	 * 是否有 属性 被编辑
	 * 
	 * @return bool
	 */
	public function isEdited($attributes = null): bool
	{
		$edited = $this->getEdited();

		if ($attributes = is_array($attributes) ? $attributes : func_get_args()) {
			return (bool)count(
				array_intersect_key($edited, array_flip($attributes))
			);
		}

		return (bool)count($edited);
	}

	/**
	 * 同步 更改属性
	 * 
	 * @return $this
	 */
	public function syncChanges()
	{
		$this->changes = $this->getEdited();

		return $this;
	}

	/**
	 * 获取 强制转换 属性
	 * 
	 * @var array
	 */
	public function getCasts(): array
	{
		return $this->casts;
	}

	/**
	 * 是否为 强制转换
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function isCast(string $key): bool
	{
		return array_key_exists($key, $this->getCasts());
	}

	/**
	 * 获取 强制转换 数据类型
	 * 
	 * @return string
	 */
	protected function getCastType(string $key): string
	{
		return $this->getCasts()[$key];
	}

	/**
	 * 强制转换 属性值
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	protected function castAttributeValue(string $key, $value)
	{
		return Transform::dataType($this->getCastType($key), $value);
	}

	/**
	 * 获取 隐藏属性
	 * 
	 * @return array
	 */
	public function getHidden(): array
	{
		return $this->hidden;
	}

	/**
	 * 转换为 数组
	 * 
	 * @return array
	 */
	public function attributesToArray(): array
	{
		$attributes = $this->getArrayableAttributes();

		// 访问器 属性
		$accessorAttributes = $this->getAccessorAttributes();
		foreach ($accessorAttributes as $key) {
			// 不存在
			if (! array_key_exists($key, $attributes)) {
				continue;
			}

			// 属性访问器值
			$attributes[$key] = $this->getAccessorAttributeValue($key, $value = $attributes[$key]);
		}

		// 强制转换 属性
		foreach ($this->getCasts() as $key => $type) {
			// 不存在 或者 有属性访问器
			if ( ! array_key_exists($key, $attributes) || in_array($key, $accessorAttributes)) {
				continue;
			}

			// 转换数据类型
			$attributes[$key] = $this->castAttributeValue($key, $attributes[$key]);
		}

		// 附加 属性
		foreach ($this->appends as $key) {
			// 属性访问器值
			$attributes[$key] = $this->getAccessorAttributeValue($key, $value = $attributes[$key]);
		}

		return $attributes;
	}

}
