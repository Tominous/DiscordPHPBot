<?php

namespace Bot\Commands;

class Khaled
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
		$reply = '';
		$quotes = [
			'Always have faith. Always have hope.',
			'The key is to make it.',
			'Another one.',
			'Key to success is clean heart and clean face.',
			'Smh they get mad when you have joy.',
			'Baby, you smart. I want you to film me taking a shower.',
			'You smart! You loyal! You a genius!',
			'Give thanks to the most high.',
			'They will try to close the door on you, just open it.',
			'They don’t want you to have the No. 1 record in the country.',
			'Those that weather the storm are the great ones.',
			'The key to success is more cocoa butter.',
			'I changed... a lot.',
			'My fans expect me to be greater and keep being great.',
			'There will be road blocks but we will overcome it.',
			'They don\'t want you to jet ski.',
			'Them doors that was always closed, I ripped the doors off, took the hinges off. And when I took the hinges off, I put the hinges on the f*ckboys’ hands.',
			'Congratulations, you played yourself.',
			'Don\'t play yourself.',
			'Another one, no. Another two, drop two singles at a time.',
		];

		for ($i = 0; $i < 3; $i++) {
			$key = array_rand($quotes);
			$reply .= $quotes[$key].PHP_EOL;
			unset($quotes[$key]);
		}


		$message->channel->sendMessage(rtrim($reply, PHP_EOL));
	}
}
