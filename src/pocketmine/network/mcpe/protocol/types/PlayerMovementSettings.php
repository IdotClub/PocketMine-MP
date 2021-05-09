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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\protocol\BedrockProtocolInfo;

final class PlayerMovementSettings{
	/** @var int */
	private $movementType;
	/** @var int */
	private $rewindHistorySize;
	/** @var bool */
	private $serverAuthoritativeBlockBreaking;

	public function __construct(int $movementType, int $rewindHistorySize, bool $serverAuthoritativeBlockBreaking){
		$this->movementType = $movementType;
		$this->rewindHistorySize = $rewindHistorySize;
		//do not ask me what the F this is doing here
		$this->serverAuthoritativeBlockBreaking = $serverAuthoritativeBlockBreaking;
	}

	public function getMovementType() : int{ return $this->movementType; }

	public function getRewindHistorySize() : int{ return $this->rewindHistorySize; }

	public function isServerAuthoritativeBlockBreaking() : bool{ return $this->serverAuthoritativeBlockBreaking; }

	public static function read(NetworkBinaryStream $in) : self{
		$rewindHistorySize = 0;
		$serverAuthBlockBreaking = false;

		if($in->protocol >= BedrockProtocolInfo::PROTOCOL_1_16_100){
			$movementType = $in->getVarInt();
			if($in->protocol >= BedrockProtocolInfo::PROTOCOL_1_16_210){
				$rewindHistorySize = $in->getVarInt();
				$serverAuthBlockBreaking = $in->getBool();
			}
		}else{
			$movementType = $in->getBool() ? PlayerMovementType::SERVER_AUTHORITATIVE_V1 : PlayerMovementType::LEGACY;
		}
		return new self($movementType, $rewindHistorySize, $serverAuthBlockBreaking);
	}

	public function write(NetworkBinaryStream $out) : void{
		if($out->protocol >= BedrockProtocolInfo::PROTOCOL_1_16_100){
			$out->putVarInt($this->movementType);
			if($out->protocol >= BedrockProtocolInfo::PROTOCOL_1_16_210){
				$out->putVarInt($this->rewindHistorySize);
				$out->putBool($this->serverAuthoritativeBlockBreaking);
			}
		}else{
			$out->putBool($this->movementType !== PlayerMovementType::LEGACY);
		}
	}
}
