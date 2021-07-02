<?php


namespace pocketmine\item;


class NetheriteBoots extends Armor{
	public function __construct(int $meta = 0){
		parent::__construct(self::NETHERITE_BOOTS, $meta, "Netherite Boots");
	}

	public function getDefensePoints() : int{
		return 3;
	}

	public function getMaxDurability() : int{
		return 482;
	}
}