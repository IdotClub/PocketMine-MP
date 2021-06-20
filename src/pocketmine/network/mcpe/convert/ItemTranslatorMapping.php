<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use function array_key_exists;

class ItemTranslatorMapping{
	/**
	 * @var int[]
	 * @phpstan-var array<int, int>
	 */
	public $simpleCoreToNetMapping = [];
	/**
	 * @var int[]
	 * @phpstan-var array<int, int>
	 */
	public $simpleNetToCoreMapping = [];

	/**
	 * runtimeId = array[internalId][metadata]
	 * @var int[][]
	 * @phpstan-var array<int, array<int, int>>
	 */
	public $complexCoreToNetMapping = [];
	/**
	 * [internalId, metadata] = array[runtimeId]
	 * @var int[][]
	 * @phpstan-var array<int, array{int, int}>
	 */
	public $complexNetToCoreMapping = [];

	public function __construct($complexCoreToNetMapping, $complexNetToCoreMapping, $simpleCoreToNetMapping, $simpleNetToCoreMapping){
		$this->complexCoreToNetMapping = $complexCoreToNetMapping;
		$this->complexNetToCoreMapping = $complexNetToCoreMapping;
		$this->simpleCoreToNetMapping = $simpleCoreToNetMapping;
		$this->simpleNetToCoreMapping = $simpleNetToCoreMapping;
	}

	/**
	 * @return int[]
	 * @phpstan-return array{int, int}
	 */
	public function toNetworkId(int $internalId, int $internalMeta) : array{
		if($internalMeta === -1){
			$internalMeta = 0x7fff;
		}
		if(isset($this->complexCoreToNetMapping[$internalId][$internalMeta])){
			return [$this->complexCoreToNetMapping[$internalId][$internalMeta], 0];
		}
		if(array_key_exists($internalId, $this->simpleCoreToNetMapping)){
			return [$this->simpleCoreToNetMapping[$internalId], $internalMeta];
		}

		throw new \InvalidArgumentException("Unmapped ID/metadata combination $internalId:$internalMeta");
	}

	/**
	 * @return int[]
	 * @phpstan-return array{int, int}
	 */
	public function fromNetworkIdWithWildcardHandling(int $networkId, int $networkMeta) : array{
		$isComplexMapping = false;
		if($networkMeta !== 0x7fff){
			return $this->fromNetworkId($networkId, $networkMeta);
		}
		[$id, $meta] = $this->fromNetworkId($networkId, 0, $isComplexMapping);
		return [$id, $isComplexMapping ? $meta : -1];
	}

	/**
	 * @return int[]
	 * @phpstan-return array{int, int}
	 */
	public function fromNetworkId(int $networkId, int $networkMeta, ?bool &$isComplexMapping = null) : array{
		if(isset($this->complexNetToCoreMapping[$networkId])){
			if($networkMeta !== 0){
				throw new \UnexpectedValueException("Unexpected non-zero network meta on complex item mapping");
			}
			$isComplexMapping = true;
			return $this->complexNetToCoreMapping[$networkId];
		}
		$isComplexMapping = false;
		if(isset($this->simpleNetToCoreMapping[$networkId])){
			return [$this->simpleNetToCoreMapping[$networkId], $networkMeta];
		}
		throw new \UnexpectedValueException("Unmapped network ID/metadata combination $networkId:$networkMeta");
	}
}