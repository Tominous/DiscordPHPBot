<?php

namespace Bot\Commands;

use Illuminate\Support\Str;

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
			$params = str_replace('```', '', $params);
			$params = "<?php\r\n".$params;
			dump($params);

			$fileName = BOT_DIR.'/eval/'.Str::random();

			file_put_contents($fileName, $params);

			// lint
			$lint = shell_exec('php -l '.$fileName);

			if (strpos($lint, 'Errors parsing') !== false) {
				$message->reply("Erorrs linting the file: ```{$lint}```");

				restore_error_handler();
				return;
			}

			$response = require_once $fileName;

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