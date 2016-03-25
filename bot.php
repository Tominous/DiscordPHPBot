<?php

ini_set('memory_limit', '-1');
define('DISCORDPHP_STARTTIME', microtime(true));
define('BOT_DIR', __DIR__);

use Bot\Bot;
use Bot\Config;
use Discord\Discord;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

include 'vendor/autoload.php';

$opts = getopt('', ['config::']);
$configfile = (isset($opts['config'])) ? $opts['config'] : $_SERVER['PWD'] . '/config.json';

$log = new Logger('DiscordPHP-Bot');
$log->pushHandler(new StreamHandler('php://stdout', Logger::INFO));

$log->addInfo('Starting DiscordPHPBot...');
$log->addInfo("Loading config from {$configfile}...");

try {
	$bot = new Bot($configfile, $log);
} catch (\Exception $e) {
	$log->addError('Error while initilizing or running the bot.', ['message' => $e->getMessage()]);
	die(1);
}

try {
	$log->addInfo('Initilizing commands...');

	$bot->addCommand('help', \Bot\Commands\Help::class, 1, 'Shows the help command.', '');
	$bot->addCommand('eval', \Bot\Commands\Evalu::class, 3, 'Evaluates some code.', '<code>');
	$bot->addCommand('join', \Bot\Commands\Join::class, 1, 'Joins the specified server.', '<invite>');
	$bot->addCommand('flush', \Bot\Commands\Flush::class, 2, 'Flushes the channels messages.', '[messages=15]');
	$bot->addCommand('info', \Bot\Commands\Info::class, 1, 'Shows information about the bot.', '');
	$bot->addCommand('meme', \Bot\Commands\Meme::class, 1, 'dank memes', '');
	$bot->addCommand('setlevel', \Bot\Commands\SetLevel::class, 4, 'Sets the auth level of a user.', '<user> [level=2]');
	$bot->addCommand('mylevel', \Bot\Commands\MyLevel::class, 0, 'Shows your auth level.', '');
	$bot->addCommand('setprefix', \Bot\Commands\SetPrefix::class, 4, 'Sets the prefix for the bot.', '<prefix>');
	$bot->addCommand('userinfo', \Bot\Commands\UserInfo::class, 1, 'Shows information about yourself or the specified user.', '[user]');
	$bot->addCommand('restart', \Bot\Commands\Restart::class, 4, 'Restarts the bot.', '');
	$bot->addCommand('coinflip', \Bot\Commands\Coinflip::class, 1, 'Does a coinflip.', '');
	$bot->addCommand('8ball', \Bot\Commands\Eightball::class, 1, 'Magic 8 Ball!', '');
	$bot->addCommand('guilds', \Bot\Commands\Guilds::class, 1, 'Shows all the guilds.', '');
	$bot->addCommand('voice', \Bot\Commands\JoinVoice::class, 1, 'Joins a voice channel.', '<channel-name>');
	$bot->addCommand('play', \Bot\Commands\PlaySong::class, 1, 'Plays a song.', '<song-name>');
	$bot->addCommand('closevoice', \Bot\Commands\CloseVoice::class, 1, 'Closes the voice connection.', '');
	$bot->addCommand('khaled', \Bot\Commands\Khaled::class, 1, 'you special.', '');
	$bot->addCommand('invite', \Bot\Commands\Invite::class, 2, 'Creates an invite for a guild.', '<guild-name>');

	$log->addInfo('initilized all commands.');
} catch (\Exception $e) {
	$log->addInfo('Error while loading all commands.', ['message' => $e->getMessage()]);
	die(1);
}

$log->addInfo('Starting the bot...');
$bot->start();