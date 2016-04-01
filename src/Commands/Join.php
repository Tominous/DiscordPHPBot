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
		if ($discord->bot) {
			$appId = isset($config['app_id']) ? $config['app_id'] : '157746770539970560';
			$message->reply("This bot can't accpet invites, sorry! Please use this OAuth invite link: https://discordapp.com/oauth2/authorize?client_id={$appId}&scope=bot&permissions=36703232");

			return;
		}

		if (preg_match('/https:\/\/discord.gg\/(.+)/', $params[0], $matches)) {
			$invite = $discord->acceptInvite($matches[1]);
			$message->reply("Joined server {$invite->guild->name}");
		}
	}
}