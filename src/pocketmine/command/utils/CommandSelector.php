<?php


namespace pocketmine\command\utils;


use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;

class CommandSelector {
	public const SELECTOR_ALL_PLAYERS = "@a";
	public const SELECTOR_ALL_ENTITIES = "@e";
	public const SELECTOR_CLOSEST_PLAYER = "@p";
	public const SELECTOR_RANDOM_PLAYER = "@r";
	public const SELECTOR_YOURSELF = "@s";
	
	private function __construct() {
		// NOOP
	}
	
	/**
	 * @return Entity[]|null
	 */
	public static function findTarget(string $selector, ?Level $level = null): ?array {
		switch (mb_strtolower($selector)) {
			case self::SELECTOR_ALL_PLAYERS:
				return Server::getInstance()->getOnlinePlayers();
			case self::SELECTOR_ALL_ENTITIES:
				if ($level !== null) {
					return $level->getEntities();
				}
				break;
			case self::SELECTOR_CLOSEST_PLAYER:
				//Deprecated
				return [];
			case self::SELECTOR_RANDOM_PLAYER:
				$players = Server::getInstance()->getOnlinePlayers();
				return count($players) > 0 ? [$players[array_rand($players)]] : [];
			case self::SELECTOR_YOURSELF:
				return null;
		}
		return [];
	}
}