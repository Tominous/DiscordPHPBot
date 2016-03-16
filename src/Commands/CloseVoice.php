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
		if (!isset($bot->voice)) {
			$message->reply('You must connect to a voice channel before closing it!');
			return;
		}

		$bot->voice->close();
		$message->reply('Leaving voice channel...');
	}
}