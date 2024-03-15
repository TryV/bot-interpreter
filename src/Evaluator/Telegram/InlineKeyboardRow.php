<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator\Telegram;

use Tryv\PhpJsonBotInterpreter\Interface\EvaluatorInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;
use SplFixedArray;

class InlineKeyboardRow extends Prototype implements EvaluatorInterface
{
	protected SplFixedArray $cols;

	protected static function getRequiredFieldNames(): array {
		return ['cols'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return false;
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		$type_checker = fn ($i) => $i instanceof InlineKeyboardButton;
		return $name === 'cols';
	}

	public function getValue()
	{
		return array_map(fn ($v) => $v instanceof ValueInterface ? $v->getValue() : $v, (array)$this->cols);
	}
}
