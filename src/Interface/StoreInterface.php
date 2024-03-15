<?php

namespace Tryv\PhpJsonBotInterpreter\Interface;

interface StoreInterface
{
	function set(string $key, mixed $value): bool;
	function get(string $key): mixed;
	function del(string $key): bool;
}

