<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator\Telegram;

use Tryv\PhpJsonBotInterpreter\Interface\Telegram\ReplyMarkupInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;

class ReplyKeyboardRemove extends Prototype implements ReplyMarkupInterface
{
	protected null|bool|ValueInterface $selective = null;

	protected static function getRequiredFieldNames(): array {
		return [];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return in_array($name, ['selective'], true);
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		return false;
	}

	public function getValue()
	{
		$arr = [
			'selective' => $this->selective,
			'remove_keyboard' => true,
		];

		return array_map(fn ($v) => $v instanceof ValueInterface ? $v->getValue() : $v, array_filter($arr, fn ($v) => $v !== null));
	}
}
