<?php

namespace DNT\Json;

interface Jsonable
{
	public static function make(string $path, bool $ensure = false, int $mode = 0755, $recursive = true): Jsonable;

	public function setPath(string $path): Jsonable;

	public function getPath(): string;

	public function get(string $key, mixed $default = null): mixed;

	public function set(string $key, mixed $value): Jsonable;

	public function toJson(int $flag = JSON_FORCE_OBJECT): string;

	public function has($offset): bool;

	public function save(): bool;

	public function setAttributes(array $attributes = []): Jsonable;

	public function all(): array;

	public function reload(): Jsonable;
}
