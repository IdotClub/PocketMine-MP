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

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\mcpe\NetworkSession as PacketHandlerInterface;
use pocketmine\network\mcpe\protocol\types\inventory\InventoryTransactionChangedSlotsHack;
use pocketmine\network\mcpe\protocol\types\inventory\MismatchTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\NormalTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\ReleaseItemTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\TransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use UnexpectedValueException as PacketDecodeException;
use function count;

class InventoryTransactionPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::INVENTORY_TRANSACTION_PACKET;

	public const TYPE_NORMAL = 0;
	public const TYPE_MISMATCH = 1;
	public const TYPE_USE_ITEM = 2;
	public const TYPE_USE_ITEM_ON_ENTITY = 3;
	public const TYPE_RELEASE_ITEM = 4;

	/** @var int */
	public $requestId;
	/** @var InventoryTransactionChangedSlotsHack[] */
	public $requestChangedSlots;
	/** @var bool */
	public $hasItemStackIds;
	/** @var TransactionData */
	public $trData;

	protected function decodePayload() : void{
		$this->requestId = $this->readGenericTypeNetworkId();
		$this->requestChangedSlots = [];
		if($this->requestId !== 0){
			for($i = 0, $len = $this->getUnsignedVarInt(); $i < $len; ++$i){
				$this->requestChangedSlots[] = InventoryTransactionChangedSlotsHack::read($this);
			}
		}

		$transactionType = $this->getUnsignedVarInt();

		$this->hasItemStackIds = $this->getBool();

		switch($transactionType){
			case self::TYPE_NORMAL:
				$this->trData = new NormalTransactionData();
				break;
			case self::TYPE_MISMATCH:
				$this->trData = new MismatchTransactionData();
				break;
			case self::TYPE_USE_ITEM:
				$this->trData = new UseItemTransactionData();
				break;
			case self::TYPE_USE_ITEM_ON_ENTITY:
				$this->trData = new UseItemOnEntityTransactionData();
				break;
			case self::TYPE_RELEASE_ITEM:
				$this->trData = new ReleaseItemTransactionData();
				break;
			default:
				throw new PacketDecodeException("Unknown transaction type $transactionType");
		}

		$this->trData->decode($this, $this->hasItemStackIds);
	}

	protected function encodePayload() : void{
		$this->writeGenericTypeNetworkId($this->requestId);
		if($this->requestId !== 0){
			$this->putUnsignedVarInt(count($this->requestChangedSlots));
			foreach($this->requestChangedSlots as $changedSlots){
				$changedSlots->write($this);
			}
		}

		$this->putUnsignedVarInt($this->trData->getTypeId());

		$this->putBool($this->hasItemStackIds);

		$this->trData->encode($this, $this->hasItemStackIds);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleInventoryTransaction($this);
	}
}
