<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator;

use Tryv\PhpJsonBotInterpreter\Interface\EvaluatorInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;

class StringMarkdownEscape extends Prototype implements EvaluatorInterface
{
	protected string|int|float|ValueInterface $text = '';

	protected static function getRequiredFieldNames(): array {
		return ['text'];
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
		$chars = ['\\', '_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
		$replacement = array_map(fn ($i) => '\\' . $i, $chars);
		return str_replace(
			$chars,
			$replacement,
			$this->text instanceof ValueInterface ? $this->text->getValue() : $this->text,
		);
	}
}
