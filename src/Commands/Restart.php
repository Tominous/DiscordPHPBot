<?php

namespace Bot\Commands;

class Restart
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
		$message->channel->sendMessage('Bot is restarting...');

		shell_exec("(sudo supervisorctl restart {$config['supervisor_process']})");
	}
}