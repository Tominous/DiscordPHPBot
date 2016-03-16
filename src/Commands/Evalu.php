<?php

namespace Bot\Commands;

class Evalu
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
			return;
		}

		set_error_handler(function ($errno, $errstr) {
			if (!(error_reporting() & $errno)) {
				return;
			}

			echo "[Eval Error] {$errno} {$errstr}\r\n";
			throw new \Exception($errstr, $errno);
		}, E_ALL);
		
		try {
			$params = implode(' ', $params);

			if (strpos(strtolower($params), 'sudo') !== false) {
				$message->reply('no sudo fgt');
				return;
			}

			eval('$response = '.$params.';');

			if (is_string($response)) {
				$response = str_replace(DISCORD_TOKEN, 'TOKEN-HIDDEN', $response);
				$response = str_replace($config['token'], 'TOKEN-HIDDEN', $response);
			}

			$message->reply("```\r\n{$response}\r\n```");
		} catch (\Exception $e) {
			$message->reply("Eval failed. {$e->getMessage()}");
		}


		restore_error_handler();
	}
}