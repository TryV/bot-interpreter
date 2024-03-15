<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator\Telegram;

use Tryv\PhpJsonBotInterpreter\Interface\Telegram\ReplyMarkupInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;
use SplFixedArray;

class InlineKeyboardMarkup extends Prototype implements ReplyMarkupInterface
{
	protected SplFixedArray $inline_keyboard;

	protected static function getRequiredFieldNames(): array {
		return ['inline_keyboard'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return false;
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		$type_checker = fn ($i) => $i instanceof InlineKeyboardRow;
		return $name === 'inline_keyboard';
	}

	public function getValue()
	{
		$rows = array_map(fn ($v) => $v instanceof ValueInterface ? $v->getValue() : $v, (array)$this->inline_keyboard);
		return '{"inline_keyboard": ' . json_encode($rows) . '}';
	}
}
