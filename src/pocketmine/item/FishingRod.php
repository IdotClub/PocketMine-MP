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

namespace pocketmine\item;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\FishingHook;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\Player;

class FishingRod extends Item{
	public function __construct(int $meta = 0){
		parent::__construct(self::FISHING_ROD, $meta, "Fishing Rod");
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : bool{
		if(!$player->hasItemCooldown($this)){
			$player->resetItemCooldown($this);

			if($player->getFishingHook() === null){
				$direction = $player->getDirectionVector();
				$radY = ($direction->y / 180) * M_PI;
				$x = cos($radY) * 0.16;
				$z = sin($radY) * 0.16;
				/** @var FishingHook $hook */
				$hook = Entity::createEntity("FishingHook",
					$player->getLevelNonNull(),
					Entity::createBaseNBT(
						$player->add(-$x, $player->getEyeHeight() - 0.10000000149011612, -$z),
						$player->getDirectionVector()->multiply(0.4)),
					$player);
				$hook->spawnToAll();
			}else{
				$player->getFishingHook()->handleHookRetraction();
				$player->getFishingHook()->flagForDespawn();
			}
			$player->broadcastEntityEvent(AnimatePacket::ACTION_SWING_ARM);
			return true;
		}
		return false;
	}
}
