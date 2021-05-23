<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\FishingRod;
use pocketmine\level\Level;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\utils\Random;
use function sqrt;

final class FishingHook extends Projectile{
	public const NETWORK_ID = self::FISHING_HOOK;

	public $height = 0.15;
	public $width = 0.15;
	protected $gravity = 0.075;
	protected $drag = 0.05;

	public function __construct(Level $level, CompoundTag $nbt, ?Entity $owner = null){
		parent::__construct($level, $nbt, $owner);

		if($owner instanceof Player){
			$owner->setFishingHook($this);
			$this->handleHookCasting($this->motion->x, $this->motion->y, $this->motion->z, 1.5, 1.0);
		}
	}

	public function handleHookCasting(float $x, float $y, float $z, float $ff1, float $ff2) : void{
		$rand = new Random();
		$f = sqrt($x * $x + $y * $y + $z * $z);
		$x /= $f;
		$y /= $f;
		$z /= $f;
		$x = $x + $rand->nextSignedFloat() * 0.007499999832361937 * $ff2;
		$y = $y + $rand->nextSignedFloat() * 0.007499999832361937 * $ff2;
		$z = $z + $rand->nextSignedFloat() * 0.007499999832361937 * $ff2;
		$x *= $ff1;
		$y *= $ff1;
		$z *= $ff1;
		$this->motion->x = $x;
		$this->motion->y = $y;
		$this->motion->z = $z;
	}

	public function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void{
		$entityHit->attack(new EntityDamageByEntityEvent($this, $entityHit, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 0));
		if($entityHit === $this->getOwningEntity()){
			$this->flagForDespawn();
			return;
		}
		$this->isCollided = true;
		$this->setTargetEntity($entityHit);
	}

	public function entityBaseTick(int $tickDiff = 1) : bool{
		$hasUpdate = parent::entityBaseTick($tickDiff);
		$owner = $this->getOwningEntity();
		if($owner instanceof Player){
			if($owner->isClosed() or !$owner->isAlive() or !($owner->getInventory()->getItemInHand() instanceof FishingRod) or $owner->distanceSquared($this) > 1024){
				$this->flagForDespawn();
			}
		}else{
			$this->flagForDespawn();
		}

		return $hasUpdate;
	}

	public function close() : void{
		parent::close();

		$owner = $this->getOwningEntity();
		if($owner instanceof Player){
			$owner->setFishingHook(null);
		}
	}

	public function handleHookRetraction() : void{
		$angler = $this->getOwningEntity();
		if($this->isValid() and $angler instanceof Player){
			$target = $this->getTargetEntity();
			if($target !== null){
				$dx = $angler->x - $this->x;
				$dy = $angler->y - $this->y;
				$dz = $angler->z - $this->z;
				$sqrt = sqrt($dx * $dx + $dy * $dy + $dz * $dz);
				$target->setMotion(
					$target->motion->add(
						$dx * 0.1,
						$dy * 0.1 + sqrt($sqrt) * 0.08,
						$dz * 0.1
					)
				);
			}
		}
	}
}