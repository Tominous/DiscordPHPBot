<?php

namespace Bot\Commands;

use Bot\Command;
use Discord\Parts\Channel\Message;
use React\Promise\Deferred;

class Invite extends Command
{
	/**
	 * {@inheritdoc}
	 */
	public function handle(Deferred $deferred, $params, Message $message, $extraData = [])
	{
		$message->reply("Bot's arent able to accept instant invites! Click this link and you will be able to invite this bot to your server. {$this->discord->application->invite_url}");
	}
}