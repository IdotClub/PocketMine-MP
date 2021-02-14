<?php


namespace pocketmine\network;


interface RawPacketHandler {
	public function handle(AdvancedSourceInterface $interface, string $address, int $port, string $payload): bool;
}