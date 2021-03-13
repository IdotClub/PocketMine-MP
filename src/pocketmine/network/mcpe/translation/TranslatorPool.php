<?php


namespace pocketmine\network\mcpe\translation;


use pocketmine\network\mcpe\protocol\BedrockProtocolInfo;

class TranslatorPool {
	/** @var Translator[] */
	private static $translators = [];

	public static function init() : void {

	}

	public static function getTranslator(int $protocol) : ?Translator {
		return self::$translators[self::translateProtocol($protocol)] ?? null;
	}

	public static function translateProtocol(int $protocol) : int {
		return $protocol >= BedrockProtocolInfo::PROTOCOL_1_16_20 ? BedrockProtocolInfo::PROTOCOL_1_16_20 : -1;
	}
}