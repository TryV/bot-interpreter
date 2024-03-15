<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator\Telegram;

use Tryv\PhpJsonBotInterpreter\Interface\EvaluatorInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;

class InlineKeyboardButton extends Prototype implements EvaluatorInterface
{
	protected int|float|string|ValueInterface $text;
	protected null|int|float|string|ValueInterface $url = null;
	protected null|int|float|string|ValueInterface $callback_data = null;
	protected null|int|float|string|ValueInterface $switch_inline_query = null;
	protected null|int|float|string|ValueInterface $switch_inline_query_current_chat = null;
	protected null|int|float|string|ValueInterface $switch_inline_query_chosen_chat = null;
	protected null|bool|ValueInterface $pay = null;
	// there are more fields in original telegram InlineKeyboardButton

	protected static function getRequiredFieldNames(): array {
		return ['text'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return in_array($name, ['url', 'switch_inline_query', 'switch_inline_query_current_chat', 'switch_inline_query_chosen_chat', 'pay'], true);
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		return false;
	}

	public function getValue()
	{
		$arr = [
			'text' => $this->text,
			'url' => $this->url,
			'switch_inline_query' => $this->switch_inline_query,
			'switch_inline_query_current_chat' => $this->switch_inline_query_current_chat,
			'switch_inline_query_chosen_chat' => $this->switch_inline_query_chosen_chat,
			'pay' => $this->pay,
		];

		return array_map(fn ($v) => $v instanceof ValueInterface ? $v->getValue() : $v, array_filter($arr, fn ($v) => $v !== null));
	}
}
