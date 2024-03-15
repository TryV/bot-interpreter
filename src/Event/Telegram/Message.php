<?php

namespace Tryv\PhpJsonBotInterpreter\Event\Telegram;

use Tryv\PhpJsonBotInterpreter\Interface\ActionInterface;
use Tryv\PhpJsonBotInterpreter\Interface\EventInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;
use SplFixedArray;

class Message extends Prototype implements EventInterface
{
	protected SplFixedArray $body;

	protected static function getRequiredFieldNames(): array
	{
		return ['body'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return false;
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		$type_checker = fn ($i) => $i instanceof ActionInterface;

		// all of fields are arrays
		return true;
	}

	public function trigger(): void
	{
		foreach ($this->body as $action) {
			if ($action instanceof ActionInterface) $action->do();
		}
	}
}
