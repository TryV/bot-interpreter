<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator;

use Tryv\PhpJsonBotInterpreter\Prototype;
use Tryv\PhpJsonBotInterpreter\Interface\EvaluatorInterface;

class Update extends Prototype implements EvaluatorInterface {
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
		return static::array_get_by_key(static::getContextValue('update'), strtolower($this->at), /* maybe error handling */);
	}
}

