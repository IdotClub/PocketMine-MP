<?php


namespace pocketmine\network\mcpe\translation;


class TranslatorPool {
	/** @var Translator[] */
	private static $translators = [];
	
	public static function init(): void {
		self::$translators[] = new Protocol428();
	}
	
	public static function getTranslator(int $protocol): ?Translator {
		return self::$translators[self::translateProtocol($protocol)] ?? null;
	}
	
	public static function translateProtocol(int $protocol): int {
		return $protocol >= 428 ? 428 : -1;
	}
}