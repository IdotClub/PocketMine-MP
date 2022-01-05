<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\Player;
use Throwable;

class DataPacketErrorEvent extends ServerEvent {
	/** @var Player */
	private $player;
	/** @var BatchPacket */
	private $packet;
	/** @var Throwable */
	private $throwable;

	public function __construct(Player $player, BatchPacket $packet, Throwable $throwable) {
		$this->player = $player;
		$this->packet = $packet;
		$this->throwable = $throwable;
	}

	public function getPacket() : BatchPacket {
		return $this->packet;
	}

	public function getThrowable() : Throwable {
		return $this->throwable;
	}

	public function getPlayer() : Player {
		return $this->player;
	}
}
