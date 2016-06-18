<?php

namespace Bot\Commands;

use Bot\Command;
use Discord\Parts\Channel\Message;
use React\Promise\Deferred;

class Help extends Command
{
	/**
	 * {@inheritdoc}
	 */
	public function handle(Deferred $deferred, $params, Message $message, $extraData = [])
	{
		list($config, $userLevels, $levels, $commands) = $extraData;

		$str = '**Commands:**'.PHP_EOL.PHP_EOL;

		foreach ($commands as $command => $data) {
			$str .= "{$config['prefix']}{$command}: {$data['description']}".PHP_EOL;
		}

		$message->reply($str)->then(
			[$deferred, 'resolve'], [$deferred, 'reject']
		);
	}
}