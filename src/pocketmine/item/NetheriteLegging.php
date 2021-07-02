<?php


namespace pocketmine\item;


class NetheriteLegging extends Armor{
	public function __construct(int $meta = 0){
		parent::__construct(self::NETHERITE_LEGGINGS, $meta, "Netherite Leggings");
	}

	public function getDefensePoints() : int{
		return 6;
	}

	public function getMaxDurability() : int{
		return 556;
	}
}