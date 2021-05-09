<?php

declare(strict_types=1);

namespace pocketmine;

use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use function get_class;
use function gettype;
use function is_object;
use function microtime;
use function strtolower;

class OfflinePlayerDataManager{
	/** @var \LevelDB */
	private $db;
	/** @var string */
	private $path;

	public function __construct(string $path){
		$this->path = $path;
		$this->db = new \LevelDB($path, [
			"compression" => LEVELDB_ZLIB_RAW_COMPRESSION
		]);
	}

	public function close() : void{
		unset($this->db);
	}

	public function saveOfflinePlayerData(string $name, CompoundTag $nbtTag) : void{
		$ev = new PlayerDataSaveEvent($nbtTag, $name);
		$ev->setCancelled(!Server::getInstance()->shouldSavePlayerData());

		$ev->call();

		if(!$ev->isCancelled()){
			$nbt = new BigEndianNBTStream();
			try{
				$this->db->put(strtolower($name), $nbt->writeCompressed($ev->getSaveData()));
			}catch(\Throwable $e){
				Server::getInstance()->getLogger()->critical(Server::getInstance()->getLanguage()->translateString("pocketmine.data.saveError", [$name, $e->getMessage()]));
				Server::getInstance()->getLogger()->logException($e);
			}
		}
	}

	public function getOfflinePlayerData(string $name) : CompoundTag{
		$name = strtolower($name);
		if(Server::getInstance()->shouldSavePlayerData()){
			if($this->hasOfflinePlayerData($name)){
				try{
					$nbt = new BigEndianNBTStream();
					$compound = $nbt->readCompressed($this->db->get($name));
					if(!($compound instanceof CompoundTag)){
						throw new \RuntimeException("Invalid data found in \"$name.dat\", expected " . CompoundTag::class . ", got " . (is_object($compound) ? get_class($compound) : gettype($compound)));
					}

					return $compound;
				}catch(\Throwable $e){ //zlib decode error / corrupt data
					$this->db->delete($name);
					Server::getInstance()->getLogger()->notice(Server::getInstance()->getLanguage()->translateString("pocketmine.data.playerCorrupted", [$name]));
				}
			}else{
				Server::getInstance()->getLogger()->notice(Server::getInstance()->getLanguage()->translateString("pocketmine.data.playerNotFound", [$name]));
			}
		}
		$spawn = Server::getInstance()->getDefaultLevel()->getSafeSpawn();
		$currentTimeMillis = (int) (microtime(true) * 1000);

		$nbt = new CompoundTag("", [
			new LongTag("firstPlayed", $currentTimeMillis),
			new LongTag("lastPlayed", $currentTimeMillis),
			new ListTag("Pos", [
				new DoubleTag("", $spawn->x),
				new DoubleTag("", $spawn->y),
				new DoubleTag("", $spawn->z)
			], NBT::TAG_Double),
			new StringTag("Level", Server::getInstance()->getDefaultLevel()->getFolderName()),
			//new StringTag("SpawnLevel", $this->getDefaultLevel()->getFolderName()),
			//new IntTag("SpawnX", $spawn->getFloorX()),
			//new IntTag("SpawnY", $spawn->getFloorY()),
			//new IntTag("SpawnZ", $spawn->getFloorZ()),
			//new ByteTag("SpawnForced", 1), //TODO
			new ListTag("Inventory", [], NBT::TAG_Compound),
			new ListTag("EnderChestInventory", [], NBT::TAG_Compound),
			new IntTag("playerGameType", Server::getInstance()->getGamemode()),
			new ListTag("Motion", [
				new DoubleTag("", 0.0),
				new DoubleTag("", 0.0),
				new DoubleTag("", 0.0)
			], NBT::TAG_Double),
			new ListTag("Rotation", [
				new FloatTag("", 0.0),
				new FloatTag("", 0.0)
			], NBT::TAG_Float),
			new FloatTag("FallDistance", 0.0),
			new ShortTag("Fire", 0),
			new ShortTag("Air", 300),
			new ByteTag("OnGround", 1),
			new ByteTag("Invulnerable", 0),
			new StringTag("NameTag", $name)
		]);

		return $nbt;

	}

	/**
	 * Returns whether the server has stored any saved data for this player.
	 */
	public function hasOfflinePlayerData(string $name) : bool{
		return $this->db->get(strtolower($name)) !== false;
	}
}