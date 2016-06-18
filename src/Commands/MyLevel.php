<?php

namespace Bot\Commands;

use Bot\Command;
use Discord\Parts\Channel\Message;
use React\Promise\Deferred;

class MyLevel extends Command
{
	/**
	 * {@inheritdoc}
	 */
	public function handle(Deferred $deferred, $params, Message $message, $extraData = [])
	{
		list($levels) = $extraData;
		
		$message->reply('Your level is '.$levels[$this->level]);
	}
}