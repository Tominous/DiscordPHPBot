<?php

namespace Bot\Commands;

class Fancy
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
		if (! isset($params[0])) {
			$message->reply('Please provide a string to make fancy!');

			return;
		}

		$orig = implode(' ', $params);
		$string = str_split($orig);
		$finalString = '';

		$fancy = function () use (&$fancy, &$string, &$finalString, $orig) {
			$finalString .= implode(' ', $string).PHP_EOL;
			$string[] = array_shift($string);

			if (implode('', $string) != $orig) {
				$fancy();
			}
		};

		$fancy();

		$message->reply("```{$finalString}```");
	}
}