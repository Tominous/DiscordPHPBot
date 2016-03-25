<?php

namespace Bot\Commands;

use Bot\Config;

class SetPrefix
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
		$prefix = (isset($params[0])) ? $params[0] : $config['prefix'];
		$config['prefix'] = $prefix;
		Config::saveConfig($config, $config['filename']);

		$message->reply("Set the prefix to `{$prefix}`");
	}
}