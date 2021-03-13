<?php


namespace pocketmine\network\mcpe\convert;


use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\SingletonTrait;

class BlockRuntimeTranslator {
	use SingletonTrait;

	/** @var array<int, int[]> */
	private $mapping = [];

	public function __construct() {
		$runtimeMapping = file_get_contents(\pocketmine\RESOURCE_PATH . "pallet/408_422_runtime.json");
		if ($runtimeMapping === false) {
			throw new AssumptionFailedError("Missing required resource file");
		}
		/** @var array<string, int> */
		$data = json_decode($runtimeMapping, true);
		$newData = [];

		foreach ($data as $new => $old) {
			$newData[(int) $new] = $old;
		}
		$this->mapping[408] = $newData;
	}

	public function translate(int $protocol, int $runtime) : ?int {
		return $this->mapping[$protocol][$runtime] ?? null;
	}
}