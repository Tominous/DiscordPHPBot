<?php

namespace Bot\Commands;

use Bot\Config;

class SetLevel
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
		if (isset($params[0])) {
			$user = $params[0];
			$level = (isset($params[1])) ? $params[1] : 2;

			if (preg_match('/<@([0-9]+)>/', $user, $matches)) {
				$user = $matches[1];
			}

			$config['perms']['perms'][$user] = $level;

			Config::saveConfig($config);

			$message->reply("Set user <@{$user}> auth level to {$config['perms']['levels'][$level]}");
		}
	}
}