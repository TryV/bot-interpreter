<?php

namespace Tryv\PhpJsonBotInterpreter\Trait;

use Tryv\PhpJsonBotInterpreter\Bot;

trait Telegram {
	abstract protected static function getContextValue(string $key, $fallback = null): mixed;

	public function bot(): Bot
	{
		return static::getContextValue('tg');
	}
}
