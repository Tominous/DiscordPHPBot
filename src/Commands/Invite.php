<?php

namespace Bot\Commands;

use Discord\Exceptions\DiscordRequestFailedException;

class Invite
{
	/**
	 * Handles the message.
	 *
	 * @param Message $message 
	 * @param array $params
	 * @param Discord $discord 
	 * @param Config $config 
	 * @param Bot $bot 
	 * @return void 
	 */
	public static function handleMessage($message, $params, $discord, $config, $bot)
	{
		if (!isset($params[1])) {
			$message->reply('Please pass through a guild name.');
			return;
		}

		$guild = implode(' ', $params);
		$guild = $discord->guilds->get('name', $guild);

		if (is_null($guild)) {
			$message->reply('Could not find the guild!');
			return;
		}

		foreach ($guild->channels as $channel) {
			try {
				$invite = $channel->createInvite();
			} catch (DiscordRequestFailedException $e) {
				echo "Error attempting to create invite: {$e->getMessage()}\r\n";
				continue;
			}

			$message->author->sendMessage("Invite: {$invite->invite_url}");
			$message->reply('Invite sent in PM.');

			return;
		}

		$message->reply('Was unable to create an invite for "'.$guild->name.'"');
	}
}