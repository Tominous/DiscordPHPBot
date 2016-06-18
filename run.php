<?php

ini_set('memory_limit', '-1');
define('DISCORDPHP_STARTTIME', microtime(true));
define('BOT_DIR', __DIR__);

use Bot\Commands\Help;
use Bot\Commands\Info;
use Bot\Commands\Invite;
use Bot\Commands\Meme;
use Bot\Commands\MyLevel;
use Discord\Discord;
use React\Promise\Deferred;

include __DIR__.'/vendor/autoload.php';

$config = json_decode(file_get_contents(__DIR__.'/config.json'), true);
$userLevels = json_decode(file_get_contents(__DIR__.'/levels.json'), true);
$levels = [
   -1 => 'Banned',
    0 => 'User',
    1 => 'Moderator',
    2 => 'Administrator',
    3 => 'Owner',
];
$commands = [
    'help' => [
        'class' => Help::class,
        'description' => 'Shows the commands the bot has available for the user.',
        'level' => 0,
        'extraData' => [$config, $userLevels, $levels],
    ],
    'info' => [
        'class' => Info::class,
        'description' => 'Shows information about the bot.',
        'level' => 0,
        'extraData' => [],
    ],
    'invite' => [
        'class' => Invite::class,
        'description' => 'Returns the bot invite URL.',
        'level' => 0,
        'extraData' => [],
    ],
    'meme' => [
        'class' => Meme::class,
        'description' => 'memes',
        'level' => 0,
        'extraData' => [],
    ],
    'mylevel' => [
        'class' => MyLevel::class,
        'description' => 'Shows you your level.',
        'level' => 0,
        'extraData' => [$levels],
    ],
];

$commands['help']['extraData'][] = &$commands;

$discord = new Discord($config['options']);

$discord->on('ready', function ($discord) use (&$config, &$commands, &$userLevels) {
    $discord->on('message', function ($message, $discord) use (&$config, &$commands, &$userLevels) {
        $prefix = substr($message->content, 0, strlen($config['prefix']));
        $withoutPrefix = substr($message->content, strlen($config['prefix']), strlen($message->content));

        if ($prefix !== $config['prefix']) {
            return;
        }

        $params = explode(' ', $withoutPrefix);
        $command = array_shift($params);

        if (array_key_exists($command, $commands)) {
            $commandConfig = $commands[$command];

            if (isset($userLevels[$message->author->id])) {
                $userLevel = $userLevels[$message->author->id];
            } else {
                $userLevel = 0;
            }

            if ($commandConfig['level'] > $userLevel) {
                $message->reply('You do not have access to this command!');
                $discord->logger->warning('user tried to access higher level command', ['user' => $message->author, 'command' => $command, 'params' => $params]);

                return;
            }

            $deferred = new Deferred();
            $handler = new $commandConfig['class']($discord, $userLevel);

            $deferred->promise()
                ->then(function ($result) use ($command, $params, $message, $discord) {
                    $discord->logger->info('handled command', ['command' => $command, 'user' => $message->author, 'params' => $params]);
                })
                ->otherwise(function ($e) use ($command, $params, $message, $discord) {
                    $discord->logger->error('error while handling command', ['command' => $command, 'user' => $message->author, 'params' => $params, 'e' => $e->getMessage()]);
                });

            $handler->handle($deferred, $params, $message, $commandConfig['extraData']);
        }
    });
});

$discord->run();