<?php

namespace Tryv\PhpJsonBotInterpreter\Interface;

interface ScopeInterface
{
	function getLines(): \IteratorAggregate|array;
	function shouldRun(): bool;
}

