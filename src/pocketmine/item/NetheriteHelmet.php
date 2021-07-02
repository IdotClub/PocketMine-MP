<?php


namespace pocketmine\item;


class NetheriteHelmet extends Armor{
	public function __construct(int $meta = 0){
		parent::__construct(self::NETHERITE_HELMET, $meta, "Netherite Helmet");
	}

	public function getDefensePoints() : int{
		return 3;
	}

	public function getMaxDurability() : int{
		return 408;
	}
}