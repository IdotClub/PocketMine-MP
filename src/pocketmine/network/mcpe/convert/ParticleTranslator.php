<?php


namespace pocketmine\network\mcpe\convert;


use pocketmine\network\mcpe\protocol\BedrockProtocolInfo;

class ParticleTranslator{
	public static function translateId(int $id, int $protocol) : int{
		if($id >= 9 && $protocol >= BedrockProtocolInfo::PROTOCOL_1_17_10 && $id <= 80){
			return $id + 1;
		}
		return $id;
	}
}