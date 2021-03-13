<?php


namespace pocketmine\network\mcpe\translation;


use pocketmine\network\mcpe\protocol\BedrockProtocolInfo;

class TranslatorPool {
	/** @var Translator[] */
	private static $translators = [];

	public static function init() : void {
		self::$translators[BedrockProtocolInfo::PROTOCOL_1_16_20] = new Protocol408();
	}

	public static function getTranslator(int $protocol) : ?Translator {
		var_dump($protocol);
		return self::$translators[BedrockProtocolInfo::PROTOCOL_1_16_20] ?? null;
	}
}