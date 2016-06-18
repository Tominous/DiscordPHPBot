<?php

namespace Bot;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use React\Promise\Deferred;

abstract class Command
{
	/**
	 * The Discord client.
	 *
	 * @var Discord Client.
	 */
	protected $discord;

	/**
	 * The user level.
	 *
	 * @var int Level.
	 */
	protected $level;

	/**
	 * Constructs the command class.
	 *
	 * @param Discord $discord The Discord client.
	 * @param int     $level   The user level.
	 */
	public function __construct(Discord $discord, $level)
	{
		$this->discord = $discord;
		$this->level = $level;
	}

	/**
	 * Handles the command.
	 *
	 * @param Deferred $deferred  A deferred promise.
	 * @param array    $params    An array of parameters.
	 * @param Message  $message   The Message object.
	 * @param int      $level     The level the user has.
	 * @param array    $extraData Extra data passed to the command.
	 */
	abstract public function handle(Deferred $deferred, $params, Message $message, $extraData = []);
}