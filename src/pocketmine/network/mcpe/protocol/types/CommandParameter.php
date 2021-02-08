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

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class CommandParameter {
	public const FLAG_FORCE_COLLAPSE_ENUM = 0x1;
	public const FLAG_HAS_ENUM_CONSTRAINT = 0x2;
	
	/** @var string */
	public $paramName;
	/** @var int */
	public $paramType;
	/** @var bool */
	public $isOptional;
	/** @var int */
	public $flags = 0; //shows enum name if 1, always zero except for in /gamerule command
	/** @var CommandEnum|null */
	public $enum;
	/** @var string|null */
	public $postfix;

	/**
	 * @param CommandEnum|string $extraData
	 */
	public function __construct(string $name = "args", int $type = AvailableCommandsPacket::ARG_TYPE_RAWTEXT, bool $optional = true, $extraData = null, int $flags = 0) {
		$this->paramName = $name;
		$this->paramType = $type | AvailableCommandsPacket::ARG_FLAG_VALID;
		$this->isOptional = $optional;
		if ($extraData instanceof CommandEnum) {
			$this->enum = $extraData;
		} else if (is_string($extraData)) {
			$this->postfix = $extraData;
		}
		$this->flags = $flags;
	}
}
