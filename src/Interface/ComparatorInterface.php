<?php

namespace Tryv\PhpJsonBotInterpreter\Interface;

interface ComparatorInterface extends ValueInterface
{
	function getValue(): bool;
}
