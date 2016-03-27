<?php

namespace Bot;

use Bot\Commands\Khaled;
use Bot\Config;
use Discord\Cache\Cache;
use Discord\Cache\Drivers\RedisCacheDriver;
use Discord\Discord;
use Discord\WebSockets\Event;
use Discord\WebSockets\WebSocket;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Bot
{
	/**
	 * The Discord instance.
	 *
	 * @var Discord 
	 */
	protected $discord;

	/**
	 * The Discord WebSocket instance.
	 *
	 * @var WebSocket 
	 */
	public $websocket;

	/**
	 * The VoiceClient instance (if applicable).
	 *
	 * @var VoiceClient 
	 */
	public $voice;

	/**
	 * The list of commands.
	 *
	 * @var array 
	 */
	protected $commands = [];

	/**
	 * The config file.
	 *
	 * @var string
	 */
	protected $configfile;

	/**
	 * Monolog logger.
	 *
	 * @var Logger The logger.
	 */
	protected $log;

	/**
	 * Constructs the bot instance.
	 *
	 * @param string $configfile 
	 * @param Logger $log 
	 * @return void 
	 */
	public function __construct($configfile, $log)
	{
		$this->configfile = $configfile;
		$this->log = $log;

		$config = Config::getConfig($this->configfile);
		$this->log->addInfo('Loaded config.', $config);

		$this->discord = Discord::createWithBotToken($config['token']);
		$this->websocket = new WebSocket($this->discord);	
	}

	/**
	 * Adds a command.
	 *
	 * @param string $command 
	 * @param string $class 
	 * @param integer $perms
	 * @param string $description 
	 * @param string $usage 
	 * @return void 
	 */
	public function addCommand($command, $class, $perms, $description, $usage)
	{
		$this->commands[$command] = [
			'perms' => $perms,
			'class' => $class,
			'description' => $description,
			'usage'	=> $usage
		];
	}

	/**
	 * Starts the bot.
	 *
	 * @return void 
	 */
	public function start()
	{
		// set_error_handler(function ($errno, $errstr) {
		// 	if (!(error_reporting() & $errno)) {
		// 		return;
		// 	}

		// 	echo "[Error] {$errno} {$errstr}\r\n";
		// 	throw new \Exception($errstr, $errno);
		// }, E_ALL);

		$this->websocket->on(Event::MESSAGE_CREATE, function ($message, $discord, $new) {
			$config = Config::getConfig($this->configfile);

			foreach ($this->commands as $command => $data) {
				$parts = [];
				$content = explode(' ', $message->content);

				foreach ($content as $index => $c) {
					foreach (explode("\n", $c) as $p) {
						$parts[] = $p;
					}
				}

				$content = $parts;

				if ($content[0] == $config['prefix'] . $command) {
					array_shift($content);
					$user_perms = @$config['perms']['perms'][$message->author->id];

					if (empty($user_perms)) {
						$user_perms = $config['perms']['default'];
					}

					if ($user_perms >= $data['perms']) {
						try {
							$data['class']::handleMessage($message, $content, $new, $config, $this);
							$this->log->addInfo("{$message->author->username}#{$message->author->discriminator} ({$message->author}) ran command {$config['prefix']}{$command}", $content);
						} catch (\Exception $e) {
							try {
								$this->log->addError("Error running the command {$config['prefix']}{$command}", ['message' => $e->getMessage()]);
								$message->reply("There was an error running the command. `{$e->getMessage()}`");
							} catch (\Exception $e2) {}
						}
					} else {
						try {
							$message->reply('You do not have permission to do this!');
						} catch (\Exception $e2) {}
						$this->log->addWarning("{$message->author->username}#{$message->author->discriminator} ({$message->author}) attempted to run command {$config['prefix']}{$command}", $content);
					}
				}
			}
		});

		$this->websocket->on(Event::MESSAGE_CREATE, function ($message, $discord, $new) {
			$triggers = [
				'bless up',
				':pray:',
				'🙏'
			];

			if (
				Str::contains(strtolower($message->content), $triggers) && $message->author->id != $discord->id
			) {
				$config = Config::getConfig($this->configfile);
				$content = explode(' ', $message->content);
				Arr::forget($content, 0);

				Khaled::handleMessage($message, $content, $new, $config, $this);
			}
		});

		$this->websocket->on(Event::MESSAGE_CREATE, function ($message, $discord, $new) {
			if ($message->author->id == '81726071573061632' && strtolower($message->content) == 'we dem') {
				$message->channel->sendMessage('BOIZ');
			}
		});

		$this->websocket->on('ready', function ($discord) {
			$this->log->addInfo('WebSocket is ready.');
			$discord->updatePresence($this->websocket, 'DiscordPHP '.Discord::VERSION, false);
		});

		$this->websocket->on('error', function ($error, $ws) {
			$this->log->addError("WebSocket encountered an error: {$error}", [$error]);
		});

		// $this->websocket->on('heartbeat', function ($epoch) {
		// 	echo "Heartbeat at {$epoch}\r\n";
		// });

		$this->websocket->on('close', function ($op, $reason) {
			$this->log->addWarning("WebSocket closed.", ['code' => $op, 'reason' => $reason]);
		});

		$this->websocket->on('reconnecting', function () {
			$this->log->addInfo('WebSocket is reconnecting...');
		});

		$this->websocket->on('reconnected', function () {
			$this->log->addInfo('WebSocket has reconnected.');
		});

		$config = Config::getConfig($this->configfile);
		if (isset($config['cache']) && $config['cache'] == 'redis') {
			Cache::setCache(new RedisCacheDriver('localhost'));
		}

		if (isset($config['carbon_bot']) && $config['carbon_bot']['enabled']) {
			$guzzle = new Client(['http_errors' => false]);
			$body = [
				'key' => $config['carbon_bot']['key'],
			];
			$this->log->addInfo('Enabling Carbon server count updates...');

			$carbonHeartbeat = function () use ($guzzle, &$body) {
				$body['servercount'] = $this->discord->guilds->count();

				$this->log->addDebug('Sending Carbon server count update...');

				$request = new Request(
					'POST',
					'https://www.carbonitex.net/discord/data/botdata.php',
					['Content-Type' => 'application/json'],
					json_encode($body)
				);

				$response = $guzzle->send($request);

				if ($response->getStatusCode() !== 200) {
					$this->log->addWarning('Carbon server count update failed.', ['status' => $response->getStatusCode(), 'reason' => $response->getReasonPhrase()]);
				} else {
					$this->log->addDebug('Sent Carbon server count update successfully.');
				}
			};

			$carbonHeartbeat();

			$this->websocket->loop->addPeriodicTimer(60, $carbonHeartbeat);
		}

		$this->websocket->run();
	}

	/**
	 * Returns the list of commands.
	 *
	 * @return array 
	 */
	public function getCommands()
	{
		return $this->commands;	
	}

	/**
	 * Returns the config file.
	 *
	 * @return string 
	 */
	public function getConfigFile()
	{
		return $this->configfile;
	}
}