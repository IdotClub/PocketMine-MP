<?php


namespace pocketmine\network\mcpe\convert;


use pocketmine\utils\SingletonTrait;

class BlockRuntimeTranslator {
	use SingletonTrait;

	/** @var BlockMapping[] */
	private $mapping = [];

	public function __construct() {
		/** @var int[] $runtimeMapping */
		$runtimeMapping = json_decode((string) file_get_contents(\pocketmine\RESOURCE_PATH . "pallet/408runtime.json"), true);
		/** @var int[] $legacyMapping */
		$legacyMapping = json_decode((string) file_get_contents(\pocketmine\RESOURCE_PATH . "pallet/408legacy.json"), true);
		$this->mapping[408] = new BlockMapping($legacyMapping, $runtimeMapping);
	}

	public function translate(int $protocol, int $runtime) : ?int {
		[$id, $meta] = RuntimeBlockMapping::fromStaticRuntimeId($runtime);
		return $this->mapping[$protocol]->toStaticRuntimeId($id, $meta) ?? null;
	}
}