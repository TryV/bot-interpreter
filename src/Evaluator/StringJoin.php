<?php

namespace Tryv\PhpJsonBotInterpreter\Evaluator;

use Tryv\PhpJsonBotInterpreter\Interface\EvaluatorInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;
use SplFixedArray;

class StringJoin extends Prototype implements EvaluatorInterface
{
	protected SplFixedArray $parts;
	protected string|int|float|ValueInterface $glue = '';

	protected static function getRequiredFieldNames(): array {
		return ['parts'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return in_array($name, ['glue']);
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		$type_checker = fn ($i) => is_string($i) || is_integer($i) || is_float($i) || is_null($i) || $i instanceof ValueInterface;
		return $name === 'parts';
	}

	public function getValue()
	{
		return implode(
			$this->glue instanceof ValueInterface ? $this->glue->getValue() : $this->glue,
			array_map(fn ($i) => (string)($i instanceof ValueInterface ? $i->getValue() : $i), (array)$this->parts)
		);
	}
}
