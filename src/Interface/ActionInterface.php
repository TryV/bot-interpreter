<?php

namespace Tryv\PhpJsonBotInterpreter\Interface;

interface ActionInterface
{
	function do(): void;
	function isDone(): bool;
	function isSuccess(): bool;
}
