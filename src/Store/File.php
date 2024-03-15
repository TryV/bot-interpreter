<?php

namespace Tryv\PhpJsonBotInterpreter\Store;

use Tryv\PhpJsonBotInterpreter\Interface\StoreInterface;
use RuntimeException;

class File implements StoreInterface
{
	private $path_sep = ':';

	public function __construct(private string $directory, private string $namespace = '')
	{
		$this->directory = realpath($directory);
		if (is_dir($this->directory) === false)
			throw new RuntimeException('Directory doesn\'t exists');
	}

	private function generateKey(string $key)
	{
		$join = $this->namespace . $this->path_sep . $key;
		return md5($join);
	}

	private function generatePath(string $key)
	{
		return $this->directory . DIRECTORY_SEPARATOR . '.' . $this->generateKey($key);
	}

	public function get(string $key): mixed
	{
		$path = $this->generatePath($key);
		if (is_dir($path)===true || is_readable($path) === false) return null;

		return unserialize(file_get_contents($path));
	}

	public function set(string $key, mixed $value): bool
	{
		$path = $this->generatePath($key);
		if (is_dir($path)===true) throw new RuntimeException('Directory "' . $path . '" isn\'t writable');

		return (bool)file_put_contents($path, serialize($value));
	}

	public function del(string $key): bool
	{
		$path = $this->generatePath($key);
		if (is_dir($path)===true) throw new RuntimeException('Directory "' . $path . '" isn\'t writable');

		return (bool)unlink($path);
	}
}
