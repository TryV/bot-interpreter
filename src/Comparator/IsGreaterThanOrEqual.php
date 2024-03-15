<?php

namespace Tryv\PhpJsonBotInterpreter\Comparator;

use Tryv\PhpJsonBotInterpreter\Interface\ComparatorInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;

class IsGreaterThanOrEqual extends Prototype implements ComparatorInterface
{
	protected $left;
	protected $right;

	protected static function getRequiredFieldNames(): array {
		return ['left', 'right'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return false;
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		return false;
	}
	
	public function getValue(): bool
	{
		return (
			$this->left instanceof ValueInterface ? $this->left->getValue() : $this->left
		) >= (
			$this->right instanceof ValueInterface ? $this->right->getValue() : $this->right
		);
	}
}

