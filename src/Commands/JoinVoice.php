<?php

namespace Bot\Commands;

use Discord\Exceptions\DiscordRequestFailedException;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Guild\Guild;
use Discord\Voice\VoiceClient;

class JoinVoice
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
			$message->reply('Please enter a channel name!');
			return;
		}

		$channelName = implode(' ', $params);
		$channel = $message->full_channel->guild->channels->get('name', $channelName);

		if (is_null($channel)) {
			$message->reply('Couldn\'t find that channel!');

			return;
		}

		$bot->websocket->joinVoiceChannel($channel)->then(
			// Joining was a success
			function (VoiceClient $vc) use ($message, &$bot) {
				$message->reply("Joined voice channel.");

				$vc->on('stderr', function ($data) use ($message) {
					$message->channel->sendMessage("**stderr:** {$data}");
				});
			},
			// Joining failed!
			function ($e) use ($message) {
				$message->reply("Oops, there was an error joining the voice channel: {$e->getMessage()}");
			}
		);
	}
}