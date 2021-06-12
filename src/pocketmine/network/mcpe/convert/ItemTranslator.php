<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\network\mcpe\protocol\BedrockProtocolInfo;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\SingletonTrait;
use function file_get_contents;
use function is_array;
use function is_numeric;
use function is_string;
use function json_decode;

/**
 * This class handles translation between network item ID+metadata to PocketMine-MP internal ID+metadata and vice versa.
 */
final class ItemTranslator{
	use SingletonTrait;

	/** @var ItemTranslatorMapping[] */
	private $mappings = [];

	/**
	 * @param int[]   $simpleMappings
	 * @param int[][] $complexMappings
	 *
	 * @phpstan-param array<string, int> $simpleMappings
	 * @phpstan-param array<string, array<int, int>> $complexMappings
	 */
	public function __construct(ItemTypeDictionary $dictionary, array $simpleMappings, array $complexMappings){
		$default = new ItemTranslatorMapping([], [], [], []);
		foreach($dictionary->getDictionary()->getEntries() as $entry){
			$stringId = $entry->getStringId();
			$netId = $entry->getNumericId();
			if(isset($complexMappings[$stringId])){
				[$id, $meta] = $complexMappings[$stringId];
				$default->complexCoreToNetMapping[$id][$meta] = $netId;
				$default->complexNetToCoreMapping[$netId] = [$id, $meta];
			}elseif(isset($simpleMappings[$stringId])){
				$default->simpleCoreToNetMapping[$simpleMappings[$stringId]] = $netId;
				$default->simpleNetToCoreMapping[$netId] = $simpleMappings[$stringId];
			}else{
				//not all items have a legacy mapping - for now, we only support the ones that do
				continue;
			}
		}
		$this->mappings[ProtocolInfo::CURRENT_PROTOCOL] = $default;
		$this->setupJSONMapping(BedrockProtocolInfo::PROTOCOL_1_17_0);
	}

	private function setupJSONMapping(int $protocol) : void{
		$this->mappings[$protocol] = new ItemTranslatorMapping(
			json_decode(
				file_get_contents(\pocketmine\RESOURCE_PATH .
					"/item_dictionary/{$protocol}_complexCoreToNetMapping.json"), true
			),
			json_decode(
				file_get_contents(\pocketmine\RESOURCE_PATH .
					"/item_dictionary/{$protocol}_complexNetToCoreMapping.json"), true
			),
			json_decode(
				file_get_contents(\pocketmine\RESOURCE_PATH .
					"/item_dictionary/{$protocol}_simpleCoreToNetMapping.json"), true
			),
			json_decode(
				file_get_contents(\pocketmine\RESOURCE_PATH .
					"/item_dictionary/{$protocol}_simpleNetToCoreMapping.json"), true
			));
	}

	private static function make() : self{
		$data = file_get_contents(\pocketmine\RESOURCE_PATH . '/vanilla/r16_to_current_item_map.json');
		if($data === false) throw new AssumptionFailedError("Missing required resource file");
		$json = json_decode($data, true);
		if(!is_array($json) or !isset($json["simple"], $json["complex"]) || !is_array($json["simple"]) || !is_array($json["complex"])){
			throw new AssumptionFailedError("Invalid item table format");
		}

		$legacyStringToIntMapRaw = file_get_contents(\pocketmine\RESOURCE_PATH . '/vanilla/item_id_map.json');
		if($legacyStringToIntMapRaw === false){
			throw new AssumptionFailedError("Missing required resource file");
		}
		$legacyStringToIntMap = json_decode($legacyStringToIntMapRaw, true);
		if(!is_array($legacyStringToIntMap)){
			throw new AssumptionFailedError("Invalid mapping table format");
		}

		/** @phpstan-var array<string, int> $simpleMappings */
		$simpleMappings = [];
		foreach($json["simple"] as $oldId => $newId){
			if(!is_string($oldId) || !is_string($newId)){
				throw new AssumptionFailedError("Invalid item table format");
			}
			if(!isset($legacyStringToIntMap[$oldId])){
				//new item without a fixed legacy ID - we can't handle this right now
				continue;
			}
			$simpleMappings[$newId] = $legacyStringToIntMap[$oldId];
		}
		foreach($legacyStringToIntMap as $stringId => $intId){
			if(isset($simpleMappings[$stringId])){
				throw new \UnexpectedValueException("Old ID $stringId collides with new ID");
			}
			$simpleMappings[$stringId] = $intId;
		}

		/** @phpstan-var array<string, array{int, int}> $complexMappings */
		$complexMappings = [];
		foreach($json["complex"] as $oldId => $map){
			if(!is_string($oldId) || !is_array($map)){
				throw new AssumptionFailedError("Invalid item table format");
			}
			foreach($map as $meta => $newId){
				if(!is_numeric($meta) || !is_string($newId)){
					throw new AssumptionFailedError("Invalid item table format");
				}
				$complexMappings[$newId] = [$legacyStringToIntMap[$oldId], (int) $meta];
			}
		}

		return new self(ItemTypeDictionary::getInstance(), $simpleMappings, $complexMappings);
	}

	public function getMapping(int $protocol = ProtocolInfo::CURRENT_PROTOCOL) : ItemTranslatorMapping{
		return $this->mappings[$protocol] ?? $this->mappings[ProtocolInfo::CURRENT_PROTOCOL];
	}
}
