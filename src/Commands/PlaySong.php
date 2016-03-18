<?php

namespace Bot\Commands;

use Illuminate\Support\Str;

class PlaySong
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
			$str = "Please provide a song:\r\n";

			foreach (glob($config['music_path'].'/*') as $song) {
				$str .= "	{$song}\r\n";
			}

			$message->reply($str);

			return;
		}

		$params = implode(' ', $params);

		if (!isset($bot->voice)) {
			$message->reply("Please run {$config['prefix']}voice before you try to play a song.");
			return;
		}

		if (!file_exists($config['music_path'].'/'.$params)) {
			$message->reply('The file '.$config['music_path'].'/'.$params.' does not exist!');
			return;
		}

		$message->reply('Playing song...');
		$bot->voice->playFile($config['music_path'].'/'.$params)->then(
			// Success
			function () use ($message) {
				$message->reply('Finished playing song.');
			},
			// Error
			function ($e) use ($message) {
				$message->reply("Error playing file: {$e->getMessage()}");
			},
			// Song Info
			function ($meta) use ($message) {
				$response = "**Song Info:**\r\n\r\n";

				$response .= "**Title:** {$meta['info']['title']}\r\n";
				$response .= "**Artist:** {$meta['info']['artist']}\r\n";
				$response .= "**Album:** {$meta['info']['album']}\r\n";

				$message->channel->sendMessage($response);

				if (isset($meta['info']['cover'])) {
					$filename = BOT_DIR.'/eval/'.Str::random().'.jpg';

					file_put_contents($filename, base64_decode($meta['info']['cover']));

					$message->channel->sendFile($filename, "Song Cover - {$meta['info']['title']}.jpg");
				}
			}
		);
	}
}