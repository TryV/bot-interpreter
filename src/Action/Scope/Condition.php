<?php

namespace Tryv\PhpJsonBotInterpreter\Action\Scope;

use Tryv\PhpJsonBotInterpreter\Interface\ActionInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ScopeInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;
use SplFixedArray;

class Condition extends Prototype implements ActionInterface
{
	protected SplFixedArray $body;
	protected null|SplFixedArray $else = null;
	protected null|ValueInterface $condition = null;

	public function isSuccess(): bool
	{
		return true;
	}

	public function isDone(): bool
	{
		return true;
	}

	protected static function getRequiredFieldNames(): array
	{
		return ['body'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return in_array($name, ['condition', 'else']);
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		$type_checker = match ($name) {
			'body' => fn ($i) => $i instanceof ActionInterface,
			'else' => fn ($i) => $i instanceof ActionInterface,
			'condition' => fn ($i) => $i instanceof ValueInterface,
		};

		return $name !== 'condition';
	}

	public function shouldRun(): bool
	{
		if ($this->condition === null) return true;
		return (bool)$this->condition->getValue();
	}

	public function do(): void
	{
		$lines = $this->shouldRun() ? $this->body : $this->else;

		if ($lines === null) return;

		foreach ($lines as $line) {
			if ($line instanceof ActionInterface) $line->do();
		}
	}
}

