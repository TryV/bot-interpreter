<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator;

use Tryv\PhpJsonBotInterpreter\Interface\EvaluatorInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;

class Env extends Prototype implements EvaluatorInterface
{
	protected string $at;

	protected static function getRequiredFieldNames(): array {
		return ['at'];
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
		return static::array_get_by_key(static::getContextValue('env'), $this->at, /* maybe error handling */);
	}
}
