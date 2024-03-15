<?php

namespace Tryv\PhpJsonBotInterpreter\Action\Telegram;

use Tryv\PhpJsonBotInterpreter\Prototype;
use Tryv\PhpJsonBotInterpreter\Interface\ActionInterface;
use Tryv\PhpJsonBotInterpreter\Interface\Telegram\ReplyMarkupInterface;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;
use Tryv\PhpJsonBotInterpreter\Trait\Telegram;

class SendMessage extends Prototype implements ActionInterface
{
	use Telegram;
	protected int|string|ValueInterface $chat_id;
	protected string|ValueInterface $text;
	protected null|int|ValueInterface $message_thread_id = null;
	protected null|string|ValueInterface $parse_mode = null;
	protected bool|ValueInterface $disable_web_page_preview = false;
	protected bool|ValueInterface $disable_notification = false;
	protected bool|ValueInterface $protect_content = false;
	protected null|int|ValueInterface $reply_to_message_id = null;
	protected bool|ValueInterface $allow_sending_without_reply = false;
	protected null|ReplyMarkupInterface $reply_markup = null;

	protected bool $is_done = false;
	protected ?bool $status = null;

	protected static function getRequiredFieldNames(): array {
		return ['chat_id', 'text'];
	}

	protected static function isExpectedFieldName(string $name): bool
	{
		return in_array($name, ['message_thread_id', 'parse_mode', 'disable_web_page_preview', 'disable_notification', 'protect_content', 'reply_to_message_id', 'allow_sending_without_reply', 'reply_markup']);
	}

	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		return false;
	}

	public function do(): void
	{
		$res = $this->bot()->sendMessage(
			chat_id: $this->chat_id,
			text: $this->text,
			message_thread_id: $this->message_thread_id,
			parse_mode: $this->parse_mode,
			disable_web_page_preview: $this->disable_web_page_preview,
			disable_notification: $this->disable_notification,
			protect_content: $this->protect_content,
			reply_to_message_id: $this->reply_to_message_id,
			allow_sending_without_reply: $this->allow_sending_without_reply,
			reply_markup: $this->reply_markup,
		);

		$this->is_done = true;
		$this->status = (bool)$res['ok'];
	}

	public function isSuccess(): bool
	{
		return $this->status;
	}

	public function isDone(): bool
	{
		return $this->is_done;
	}
}

