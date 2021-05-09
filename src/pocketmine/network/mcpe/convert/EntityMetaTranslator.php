<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\network\mcpe\protocol\BedrockProtocolInfo;
use pocketmine\utils\SingletonTrait;

final class EntityMetaTranslator{
	use SingletonTrait;

	public function translateLegacyEntityMetaId(int $id, int $protocol) : ?int{
		if($protocol >= BedrockProtocolInfo::PROTOCOL_1_16_210){
			switch($id){
				case 80:
					return 81;
				case 81:
					return 83;
			}
		}
		return null;
	}

	public function translateNewEntityMetaId(int $id, int $protocol) : ?int{
		if($protocol >= BedrockProtocolInfo::PROTOCOL_1_16_210){
			switch($id){
				case 81:
					return 80;
				case 83:
					return 81;
			}
		}
		return null;
	}
}