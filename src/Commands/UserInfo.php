<?php

namespace Bot\Commands;

use Discord\Discord;
use Discord\Helpers\Guzzle;
use Discord\Parts\User\User;

class UserInfo
{
	/**
	 * Handles the message.
	 *
	 * @param Message $message 
	 * @param array $params
	 * @param Discord $discord 
	 * @param Config $config 
	 * @return void 
	 */
	public static function handleMessage($message, $params, $discord, $config)
	{
		$id = (isset($params[0])) ? $params[0] : $message->author->id;

		if (preg_match('/<@(.+)>/', $id, $matches)) {
			$id = $matches[1];
		}

		$user = new User((array) Guzzle::get("users/{$id}"), true);

		$str  = "**Information for {$user->username}#{$user->discriminator}:**\r\n";
		$str .= "**ID:** {$user->id}\r\n";
		$str .= "**Avatar URL:** {$user->avatar}\r\n";
		$str .= "**Discriminator:** {$user->discriminator}\r\n";
		$str .= "**Mention:** `{$user}`\r\n";

		$guildcount = 0;
		$servers = '';

		foreach ($discord->guilds as $guild) {
			foreach ($guild->members as $member) {
				if ($member->id == $user->id) {
					$guildcount++;
					$servers .= $guild->name . ", ";
				}
			}
		}

		$servers = rtrim($servers, ', ');

		$str .= "**Shared Servers:** {$guildcount} _({$servers})_\r\n";

		$level = (isset($config['perms']['perms'][$user->id])) ? $config['perms']['perms'][$user->id] : $config['perms']['default'];
		$level = $config['perms']['levels'][$level];
		
		$str .= "**User Level:** {$level}\r\n\r\n";

		$roles = '';
		try {
			foreach ($message->full_channel->guild->members->get('id', $id)->roles as $role) {
				$roles .= str_replace('@everyone', '@ everyone', $role->name).', ';
			}
		} catch (\Exception $e) {
			$roles = 'Could not get roles.';
		}

		$roles = rtrim($roles, ', ');

		$str .= "**User Roles:** _{$roles}_\r\n";

		$joinedDiscord = Discord::getTimestamp($message->author->id);

		$str .= "**Joined Discord:** {$joinedDiscord}\r\n";

		$joinedGuild = $message->full_channel->guild->members->get('id', $id)->joined_at;

		$str .= "**Joined Guild:** {$joinedGuild}\r\n";

		$message->channel->sendMessage($str);
	}
}