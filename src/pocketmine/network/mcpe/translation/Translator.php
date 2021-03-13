<?php


namespace pocketmine\network\mcpe\translation;


use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\Player;

abstract class Translator {
	public abstract function translatorServer(Player $player, DataPacket &$packet) : void;

	public abstract function translatorClient(Player $player, DataPacket &$packet) : void;
}