<?php

namespace Tryv\PhpJsonBotInterpreter\Action\Store;

use Tryv\PhpJsonBotInterpreter\Interface\ActionInterface;
use Tryv\PhpJsonBotInterpreter\Interface\StoreInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;

class DeleteValue extends Prototype implements ActionInterface
{
	protected string|ValueInterface $key;

	protected bool $is_done = false;
	protected ?bool $status = null;

	public function isSuccess(): bool
	{
		return $this->status;
	}

	public function isDone(): bool
	{
		return $this->is_done;
	}

	public function do(): void
	{
		/** @var StoreInterface $store */
		$store = $this->getContextValue('store');

		$this->is_done = true;
		$this->status = $store->del($this->key instanceof ValueInterface ? $this->key->getValue() : $this->key);
	}

	protected static function getRequiredFieldNames(): array {
		return ['key'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return false;
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		return false;
	}
}
