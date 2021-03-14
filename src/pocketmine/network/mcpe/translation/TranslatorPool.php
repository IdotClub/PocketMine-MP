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
		if (in_array($protocol, [400, 406, 407, 408, 409, 410, 411, 412, 413], true)) {
			return BedrockProtocolInfo::PROTOCOL_1_16_20;
		}
		if (in_array($protocol, [414, 415, 416, 417, 418, 419, 420, 421, 422], true)) {
			return BedrockProtocolInfo::PROTOCOL_1_16_200;
		}
		if (in_array($protocol, [423, 424, 425, 426, 427, 428], true)) {
			return BedrockProtocolInfo::PROTOCOL_1_16_210;
		}
		return $protocol;
	}
}