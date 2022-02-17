<?php

namespace Test;

use DNT\Json\Exceptions\FileNotFoundException;
use DNT\Json\Json;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
	private string $path = "./test.json";

	function test_make_file_not_found()
	{
		$this->expectException(FileNotFoundException::class);
		Json::make($this->path);
		$this->assertFileDoesNotExist($this->path);
	}

	function test_make_file()
	{
		Json::make($this->path, true);

		$this->assertFileExists($this->path);
	}

	function test_set_and_get_attributes_file()
	{
		$json = Json::make($this->path, true)->setAttributes(['foo' => 'bar']);
		$this->assertSame($json->get('foo'), 'bar');
	}

	function test_array_to_json()
	{
		$array = ['foo' => 'bar'];
		$json = Json::make($this->path, true)->setAttributes($array);
		$this->assertSame($json->toJson(), json_encode($array));
	}

	function test_get_attribute()
	{
		$array = ['foo' => 'bar'];
		$json = Json::make($this->path, true)->setAttributes($array);
		$this->assertSame($json->foo, 'bar');
	}

	function test_set_attribute()
	{
		$json = Json::make($this->path, true);
		$json->foo = 'bar';
		$this->assertSame($json->foo, 'bar');
	}

	function test_save_attribute()
	{
		$array = ['foo' => 'bar'];

		$json = Json::make($this->path, true)->setAttributes($array);
		$content = json_decode(file_get_contents($this->path), JSON_OBJECT_AS_ARRAY);
		$this->assertSame($content, []);
		$json->save();

		$content = json_decode(file_get_contents($this->path), JSON_OBJECT_AS_ARRAY);
		$this->assertSame($content, $array);
	}

	function test_get_attribute_not_exists()
	{
		$json = Json::make($this->path, true);
		$this->assertNull($json->foo);
	}

	function test_set_attribute_method()
	{
		$json = Json::make($this->path, true)->set('foo', 'bar');
		$this->assertSame($json->foo, 'bar');
	}

	function test_value_is_anonymous_function()
	{
		$json = Json::make($this->path, true)->set('foo', function () {
			return 'bar';
		});
		$this->assertSame($json->foo, 'bar');
	}

	protected function tearDown(): void
	{
		if (file_exists($this->path)) {
			unlink($this->path);
		}
	}
}
