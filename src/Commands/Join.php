<?php

namespace Bot\Commands;

class Join
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
		if (preg_match('/https:\/\/discord.gg\/(.+)/', $params[0], $matches)) {
			$invite = $discord->acceptInvite($matches[1]);
			$message->reply("Joined server {$invite->guild->name}");
		}
	}
}