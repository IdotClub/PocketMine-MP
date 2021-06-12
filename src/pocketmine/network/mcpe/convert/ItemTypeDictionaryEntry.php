<?php


namespace pocketmine\network\mcpe\convert;


use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;

class ItemTypeDictionaryEntry{
	/**
	 * @var ItemTypeEntry[]
	 * @phpstan-var list<ItemTypeEntry>
	 */
	private $itemTypes;
	/**
	 * @var string[]
	 * @phpstan-var array<int, string>
	 */
	private $intToStringIdMap = [];
	/**
	 * @var int[]
	 * @phpstan-var array<string, int>
	 */
	private $stringToIntMap = [];

	/**
	 * @param ItemTypeEntry[] $itemTypes
	 */
	public function __construct(array $itemTypes){
		$this->itemTypes = $itemTypes;
		foreach($this->itemTypes as $type){
			$this->stringToIntMap[$type->getStringId()] = $type->getNumericId();
			$this->intToStringIdMap[$type->getNumericId()] = $type->getStringId();
		}
	}

	/**
	 * @return ItemTypeEntry[]
	 * @phpstan-return list<ItemTypeEntry>
	 */
	public function getEntries() : array{
		return $this->itemTypes;
	}

	public function fromStringId(string $stringId) : int{
		if(!array_key_exists($stringId, $this->stringToIntMap)){
			throw new \InvalidArgumentException("Unmapped string ID \"$stringId\"");
		}
		return $this->stringToIntMap[$stringId];
	}

	public function fromIntId(int $intId) : string{
		if(!array_key_exists($intId, $this->intToStringIdMap)){
			throw new \InvalidArgumentException("Unmapped int ID $intId");
		}
		return $this->intToStringIdMap[$intId];
	}
}