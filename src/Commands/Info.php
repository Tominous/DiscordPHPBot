<?php

namespace Bot\Commands;

use Bot\Command;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use React\Promise\Deferred;

class Info extends Command
{
	/**
	 * {@inheritdoc}
	 */
	public function handle(Deferred $deferred, $params, Message $message, $extraData = [])
	{
		$str  = "**DiscordPHP Bot**\r\n";
		$str .= "**Library:** _DiscordPHP_ ".Discord::VERSION."\r\n";

		$str .= "**PHP Version:** ".PHP_VERSION."\r\n";

		$uptime = Carbon::createFromTimestamp(DISCORDPHP_STARTTIME);
		$diff = $uptime->diff(Carbon::now());

		$str .= "**Uptime:** {$diff->d} day(s), {$diff->h} hour(s), {$diff->i} minute(s), {$diff->s} second(s)\r\n";

		$ram  = round(memory_get_usage(true)/1000000, 2);
		
		$str .= "**Memory Usage:** {$ram}mb\r\n";

		$str .= "**OS Info:** ".php_uname()."\r\n";

		$str .= "**Source:** <https://github.com/uniquoooo/DiscordPHPBot>\r\n";
		$str .= "**Library:** <https://github.com/teamreflex/DiscordPHP>\r\n";

		$str .= "\r\n**Author:** David#4618\r\n";
		$str .= "**Server Count:** {$this->discord->guilds->count()}\r\n";

		$message->reply($str)->then([$deferred, 'resolve'], [$deferred, 'reject']);
	}
}