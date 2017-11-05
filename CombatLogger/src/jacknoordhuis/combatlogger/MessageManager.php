<?php

/**
 * CombatLogger plugin for PocketMine-MP
 * Copyright (C) 2017 JackNoordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

namespace jacknoordhuis\combatlogger;

use pocketmine\utils\TextFormat as TF;

class MessageManager {

	/** @var CombatLogger */
	private $plugin;

	/** @var array */
	protected $rawMessages = [];

	/** @var array */
	protected $messages = [];

	public function __construct(CombatLogger $plugin, array $messages) {
		$this->plugin = $plugin;
		$this->rawMessages = $messages;
		$this->parseMessages();
	}

	protected function parseMessages() {
		foreach($this->rawMessages as $key => $raw) {
			$this->messages[strtolower($key)] = $this->parseMessage($raw);
		}
	}

	/**
	 * @param string $message
	 * @param string $symbol
	 *
	 * @return mixed|string
	 */
	public function parseMessage(string $message, $symbol = "&") {
		$message = str_replace($symbol . "0", TF::BLACK, $message);
		$message = str_replace($symbol . "1", TF::DARK_BLUE, $message);
		$message = str_replace($symbol . "2", TF::DARK_GREEN, $message);
		$message = str_replace($symbol . "3", TF::DARK_AQUA, $message);
		$message = str_replace($symbol . "4", TF::DARK_RED, $message);
		$message = str_replace($symbol . "5", TF::DARK_PURPLE, $message);
		$message = str_replace($symbol . "6", TF::GOLD, $message);
		$message = str_replace($symbol . "7", TF::GRAY, $message);
		$message = str_replace($symbol . "8", TF::DARK_GRAY, $message);
		$message = str_replace($symbol . "9", TF::BLUE, $message);
		$message = str_replace($symbol . "a", TF::GREEN, $message);
		$message = str_replace($symbol . "b", TF::AQUA, $message);
		$message = str_replace($symbol . "c", TF::RED, $message);
		$message = str_replace($symbol . "d", TF::LIGHT_PURPLE, $message);
		$message = str_replace($symbol . "e", TF::YELLOW, $message);
		$message = str_replace($symbol . "f", TF::WHITE, $message);

		$message = str_replace($symbol . "k", TF::OBFUSCATED, $message);
		$message = str_replace($symbol . "l", TF::BOLD, $message);
		$message = str_replace($symbol . "m", TF::STRIKETHROUGH, $message);
		$message = str_replace($symbol . "n", TF::UNDERLINE, $message);
		$message = str_replace($symbol . "o", TF::ITALIC, $message);
		$message = str_replace($symbol . "r", TF::RESET, $message);

		return $message;
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function getMessage($key) {
		return $this->messages[strtolower($key)];
	}

}