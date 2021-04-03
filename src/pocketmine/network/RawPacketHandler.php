<?php

declare(strict_types=1);

namespace pocketmine\network;

interface RawPacketHandler {
	public function handle(AdvancedSourceInterface $interface, string $address, int $port, string $payload): bool;
}