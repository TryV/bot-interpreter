<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator\Telegram;

use Tryv\PhpJsonBotInterpreter\Interface\Telegram\ReplyMarkupInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;
use SplFixedArray;

class ReplyKeyboardMarkup extends Prototype implements ReplyMarkupInterface
{
	protected SplFixedArray $keyboard;
	protected null|bool|ValueInterface $is_persistent = null;
	protected null|bool|ValueInterface $resize_keyboard = null;
	protected null|bool|ValueInterface $one_time_keyboard = null;
	protected null|int|float|string|ValueInterface $input_field_placeholder = null;
	protected null|bool|ValueInterface $selective = null;

	protected static function getRequiredFieldNames(): array {
		return ['keyboard'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return in_array($name, ['is_persistent', 'resize_keyboard', 'one_time_keyboard', 'input_field_placeholder', 'selective'], true);
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		$type_checker = fn ($i) => $i instanceof KeyboardRow;
		return $name === 'keyboard';
	}

	public function getValue()
	{
		$rows = array_map(fn ($v) => $v instanceof ValueInterface ? $v->getValue() : $v, (array)$this->keyboard);

		$arr = [
			'keyboard' => $rows,
			'is_persistent' => $this->is_persistent,
			'resize_keyboard' => $this->resize_keyboard,
			'one_time_keyboard' => $this->one_time_keyboard,
			'input_field_placeholder' => $this->input_field_placeholder,
			'selective' => $this->selective,
		];

		return array_map(fn ($v) => $v instanceof ValueInterface ? $v->getValue() : $v, array_filter($arr, fn ($v) => $v !== null));
	}
}
