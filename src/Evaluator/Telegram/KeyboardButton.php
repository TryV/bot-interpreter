<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator\Telegram;

use Tryv\PhpJsonBotInterpreter\Interface\EvaluatorInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;

class KeyboardButton extends Prototype implements EvaluatorInterface
{
	protected int|float|string|ValueInterface $text;
	protected null|bool|ValueInterface $request_contact = null;
	protected null|bool|ValueInterface $request_location = null;
	// there are more fields in original telegram KeyboardButton

	protected static function getRequiredFieldNames(): array {
		return ['text'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return in_array($name, ['request_contact', 'request_location'], true);
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		return false;
	}

	public function getValue()
	{
		$arr = [
			'text' => $this->text,
			'request_contact' => $this->request_contact,
			'request_location' => $this->request_location,
		];

		return array_map(fn ($v) => $v instanceof ValueInterface ? $v->getValue() : $v, array_filter($arr, fn ($v) => $v !== null));
	}
}
