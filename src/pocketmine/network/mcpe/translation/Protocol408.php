<?php


namespace pocketmine\network\mcpe\translation;


use pocketmine\network\mcpe\convert\BlockRuntimeTranslator;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;

class Protocol408 extends Translator {

	public function translatorServer(Player $player, DataPacket &$packet) : void {
		if ($packet instanceof UpdateBlockPacket) {
			$packet->blockRuntimeId = BlockRuntimeTranslator::getInstance()->translate($player->getProtocol(), $packet->blockRuntimeId) ?? $packet->blockRuntimeId;
		}

		if ($packet instanceof LevelEventPacket) {
			$packet->data = BlockRuntimeTranslator::getInstance()->translate($player->getProtocol(), $packet->data) ?? $packet->data;
		}
	}

	public function translatorClient(Player $player, DataPacket &$packet) : void {

	}
}