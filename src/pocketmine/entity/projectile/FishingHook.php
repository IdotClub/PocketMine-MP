<?php


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

final class FishingHook extends Projectile {
	public const NETWORK_ID = self::FISHING_HOOK;
	
	public $height = 0.25;
	public $width = 0.25;
	protected $gravity = 0.1;
	protected $drag = 0.05;
	
	public function __construct(Level $level, CompoundTag $nbt, ?Entity $owner = null) {
		parent::__construct($level, $nbt, $owner);
		
		if ($owner instanceof Player) {
			$owner->setFishingHook($this);
			$this->handleHookCasting($this->motion->x, $this->motion->y, $this->motion->z, 1.5, 1.0);
		}
	}
	
	public function handleHookCasting(float $x, float $y, float $z, float $f1, float $f2): void {
		$rand = new Random();
		$f = sqrt($x * $x + $y * $y + $z * $z);
		$x = $x / $f;
		$y = $y / $f;
		$z = $z / $f;
		$x = $x + $rand->nextSignedFloat() * 0.007499999832361937 * $f2;
		$y = $y + $rand->nextSignedFloat() * 0.007499999832361937 * $f2;
		$z = $z + $rand->nextSignedFloat() * 0.007499999832361937 * $f2;
		$x = $x * $f1;
		$y = $y * $f1;
		$z = $z * $f1;
		$this->motion->x += $x;
		$this->motion->y += $y;
		$this->motion->z += $z;
	}
	
	public function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void {
		$damage = $this->getResultDamage();
		
		if ($this->getOwningEntity() !== null) {
			$ev = new EntityDamageByEntityEvent($this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
			$entityHit->attack($ev);
			$entityHit->setMotion($this->getOwningEntity()->getDirectionVector()->multiply(0.3)->add(0, 0.3, 0));
		}
		$this->isCollided = true;
		$this->flagForDespawn();
	}
	
	public function entityBaseTick(int $tickDiff = 1): bool {
		$hasUpdate = parent::entityBaseTick($tickDiff);
		$owner = $this->getOwningEntity();
		if ($owner instanceof Player) {
			if (!$owner->getInventory()->getItemInHand() instanceof FishingRod || !$owner->isAlive() || $owner->isClosed()) {
				$this->flagForDespawn();
			}
		} else {
			$this->flagForDespawn();
		}
		
		return $hasUpdate;
	}
	
	public function close(): void {
		parent::close();
		
		$owner = $this->getOwningEntity();
		if ($owner instanceof Player) {
			$owner->setFishingHook(null);
		}
	}
}