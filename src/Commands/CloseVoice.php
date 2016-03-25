<?php

namespace Bot\Commands;

class CloseVoice
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
		$bot->websocket->getVoiceClient($message->full_channel->guild_id)->then(function ($vc) use ($message) {
			$message->reply('Leaving voice channel...');
			$vc->close();
		}, function ($e) use ($message) {
			$message->reply('Could not find a voice channel for this guild.');
		});
	}
}