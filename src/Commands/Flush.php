<?php

namespace Bot\Commands;

use Discord\Exceptions\PartRequestFailedException;

class Flush
{
	/**
	 * Handles the message.
	 *
	 * @param Message $message 
	 * @param array $params
	 * @param Discord $discord 
	 * @param Config $config 
	 * @return void 
	 */
	public static function handleMessage($message, $params, $discord, $config)
	{
		$rmmessages = (isset($params[0])) ? $params[0] : 15;
		$channel = $message->channel;
		$num = 0;
		$channel->message_count = $rmmessages + 1;

		foreach ($channel->messages as $key => $message) {
			if ($num >= $rmmessages) {
				$message->reply("Flushed {$num} messages.");
				return;
			}

			try {
				$message->delete();
			} catch (PartRequestFailedException $e) {
				continue;
			}
			$num++;
		}

		$message->reply("Flushed {$num} messages.");
	}
}