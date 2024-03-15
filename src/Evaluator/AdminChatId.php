<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator;

use Tryv\PhpJsonBotInterpreter\Interface\EvaluatorInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;

class AdminChatId extends Prototype implements EvaluatorInterface
{
	protected static function getRequiredFieldNames(): array {
		return [];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return false;
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		return false;
	}

	public function getValue()
	{
		return static::getContextValue('admin_chat_id', /* maybe error handling */);
	}
}