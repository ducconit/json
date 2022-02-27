<?php

namespace DNT\Json;

use ArrayAccess;
use DNT\Json\Exceptions\FileNotFoundException;
use DNT\Json\Exceptions\InvalidJsonException;
use DNT\Json\Exceptions\PathMustBeFileException;
use DNT\Json\Exceptions\PathNotEmptyException;
use Exception;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Stringable;

class Json implements ArrayAccess, Jsonable, JsonSerializable, Stringable
{
	use Macroable;

	private string $path;

	private bool $loaded = false;

	private array $attributes = [];

	/**
	 * @throws PathNotEmptyException
	 * @throws FileNotFoundException
	 * @throws PathMustBeFileException
	 * @throws InvalidJsonException
	 */
	private function _loadFile(bool $ensure = false, int $mode = 0755, $recursive = true): void
	{
		if ($this->loaded) {
			return;
		}
		$path = $this->getPath();
		$this->_checkPath();
		if (!file_exists($path)) {
			if (!$ensure) {
				throw new FileNotFoundException("Json: File does not exist at path {$path}.");
			}
			if (!file_exists($dir = dirname($path))) {
				$this->_makeDir($dir, $mode, $recursive);
			}
			$this->_put("{}");
		}
		if (!is_file($path)) {
			throw new PathMustBeFileException();
		}

		$content = file_get_contents($path);

		$attributes = json_decode($content, JSON_OBJECT_AS_ARRAY);
		if (json_last_error() > 0) {
			throw new InvalidJsonException('Error processing file: ' . $path . '. Error: ' . json_last_error_msg());
		}
		$this->attributes = $attributes;
		$this->loaded = true;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 * @return Jsonable
	 * @throws PathNotEmptyException
	 */
	public function setPath(string $path): Jsonable
	{
		$this->path = $path;
		$this->_checkPath($path);
		$this->loaded = false;
		return $this;
	}

	/**
	 * @throws PathNotEmptyException
	 */
	private function _checkPath($path = null): void
	{
		$path = $path ?: $this->getPath();
		if ($path == "") {
			throw new PathNotEmptyException();
		}
	}

	private function _makeDir(string $path, int $mode, bool $recursive): bool
	{
		return mkdir($path, $mode, $recursive);
	}

	private function _put($contents, $lock = false): bool|int
	{
		return file_put_contents($this->getPath(), $contents, $lock ? LOCK_EX : FILE_TEXT);
	}

	/**
	 * @throws PathMustBeFileException
	 * @throws FileNotFoundException
	 * @throws InvalidJsonException
	 * @throws PathNotEmptyException
	 */
	public static function make(string $path, bool $ensure = false, int $mode = 0755, $recursive = true): Jsonable
	{
		$instance = new static();
		$instance->setPath($path);
		$instance->_loadFile($ensure, $mode, $recursive);
		return $instance;
	}

	public function setAttributes(array $attributes = []): Jsonable
	{
		$this->attributes = $attributes;
		return $this;
	}

	public function save(): bool
	{
		try {
			$this->_put($this->toJson());
		} catch (Exception $exception) {
			return false;
		}
		return true;
	}

	public function toJson(int $flag = JSON_FORCE_OBJECT): string
	{
		return json_encode($this->attributes, $flag);
	}

	public function has($offset): bool
	{
		return array_key_exists($offset, $this->attributes);
	}

	public function set(string $key, mixed $value): Jsonable
	{
		if (is_callable($value)) {
			$value = $value();
		}
		$this->attributes[$key] = $value;
		return $this;
	}

	public function offsetExists($offset): bool
	{
		return isset($this->attributes[$offset]);
	}

	public function __get(string $name): mixed
	{
		return $this->get($name);
	}

	public function __set(string $name, $value): void
	{
		if (is_callable($value)) {
			$value = $value();
		}
		$this->attributes[$name] = $value;
	}

	public function get(string $key, mixed $default = null): mixed
	{
		try {
			return $this->attributes[$key];
		} catch (Exception $exception) {
			if (is_callable($default)) {
				return $default();
			}
			return $default;
		}
	}

	public function offsetGet($offset): mixed
	{
		return $this->attributes[$offset];
	}

	public function offsetSet($offset, $value): void
	{
		if (is_null($offset)) {
			$this->attributes[] = $value;
		} else {
			$this->attributes[$offset] = $value;
		}
	}

	public function offsetUnset($offset): void
	{
		unset($this->attributes[$offset]);
	}

	public function all(): array
	{
		return $this->attributes;
	}

	public function jsonSerialize()
	{
		return $this->attributes;
	}

	public function __toString()
	{
		return $this->toJson();
	}

	public function reload(): Jsonable
	{
		$this->loaded = false;
		return $this;
	}
}
