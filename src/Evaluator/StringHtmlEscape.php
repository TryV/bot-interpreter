<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator;

use Tryv\PhpJsonBotInterpreter\Interface\EvaluatorInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;

class StringHtmlEscape extends Prototype implements EvaluatorInterface
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
		return str_replace(
			['&', '<', '>', '"'],
			['&amp;', '&lt;', '&gt;', '&quot;'],
			$this->text instanceof ValueInterface ? $this->text->getValue() : $this->text,
		);
	}
}
