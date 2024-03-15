<?php

namespace Tryv\PhpJsonBotInterpreter\Action\Store;

use Tryv\PhpJsonBotInterpreter\Interface\ActionInterface;
use Tryv\PhpJsonBotInterpreter\Interface\StoreInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;
use SplFixedArray;

class SetValue extends Prototype implements ActionInterface
{
	protected string|ValueInterface $key;
	protected null|string|int|float|bool|SplFixedArray|ValueInterface $value;

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
		$store->set(
			$this->key instanceof ValueInterface ? $this->key->getValue() : $this->key,
			$this->value instanceof ValueInterface ? $this->value->getValue() : $this->value
		);
		$this->status = true;
	}

	protected static function getRequiredFieldNames(): array {
		return ['key', 'value'];
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
