<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\block\BlockIds;

class BlockMapping{
	/** @var int[] */
	private $legacyToRuntimeMap;
	/** @var int[] */
	private $runtimeToLegacyMap;

	/**
	 * @param int[] $legacyToRuntimeMap
	 * @param int[] $runtimeToLegacyMap
	 */
	public function __construct(array $legacyToRuntimeMap, array $runtimeToLegacyMap){
		$this->legacyToRuntimeMap = $legacyToRuntimeMap;
		$this->runtimeToLegacyMap = $runtimeToLegacyMap;
	}

	public function toStaticRuntimeId(int $id, int $meta = 0) : int{
		return $this->legacyToRuntimeMap[($id << 4) | $meta] ?? $this->legacyToRuntimeMap[$id << 4] ?? $this->legacyToRuntimeMap[BlockIds::INFO_UPDATE << 4] ?? 0;
	}

	/**
	 * @return int[] [id, meta]
	 */
	public function fromStaticRuntimeId(int $runtimeId) : array{
		$v = $this->runtimeToLegacyMap[$runtimeId];
		return [$v >> 4, $v & 0xf];
	}

	public function registerMapping(int $staticRuntimeId, int $legacyId, int $legacyMeta) : void{
		$this->legacyToRuntimeMap[($legacyId << 4) | $legacyMeta] = $staticRuntimeId;
		$this->runtimeToLegacyMap[$staticRuntimeId] = ($legacyId << 4) | $legacyMeta;
	}
}