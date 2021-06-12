<?php


namespace pocketmine\network\mcpe\convert;


use pocketmine\utils\AssumptionFailedError;

final class EntityTypeDictionary{
	/** @var int[] */
	private static $stringToIntMap = [];
	/** @var string[] */
	private static $intToStringMap = [];
	private static $init = false;

	public static function fromStringId(string $id) : int{
		if(!self::$init){
			self::init();
		}
		$ret = self::$stringToIntMap[$id] ?? null;
		if($ret !== null){
			return $ret;
		}
		throw new \InvalidArgumentException("Unmapped string entity ID $id");
	}

	private static function init(){
		$map = file_get_contents(\pocketmine\RESOURCE_PATH . "vanilla/entity_id_map.json");
		if($map === false){
			throw new AssumptionFailedError("Missing required resource file");
		}
		$map = json_decode($map, true);
		if(!is_array($map)){
			throw new AssumptionFailedError("entity_id_map.json root should contain a map of entity id");
		}
		foreach($map as $stringId => $intId){
			self::$stringToIntMap[$stringId] = $intId;
			self::$intToStringMap[$intId] = $stringId;
		}
		self::$init = true;
	}

	public static function toStringId(int $id) : string{
		if(!self::$init){
			self::init();
		}
		return self::$intToStringMap[$id] ?? ":";
	}
}