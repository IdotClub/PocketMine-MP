<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use function in_array;

final class BedrockProtocolInfo{
	public const PROTOCOL_1_16_100 = 419;
	public const PROTOCOL_1_16_200 = 422;
	public const PROTOCOL_1_16_210 = 428;
	public const PROTOCOL_1_16_220 = 431;
	public const PROTOCOL_1_17_0 = 440;
	public const PROTOCOL_1_17_10 = 448;

	public static function translateProtocol(int $protocol) : int{
		if(in_array($protocol, [414, 415, 416, 417, 418, 419], true)){
			return BedrockProtocolInfo::PROTOCOL_1_16_100;
		}
		if(in_array($protocol, [420, 421, 422], true)){
			return BedrockProtocolInfo::PROTOCOL_1_16_200;
		}
		if(in_array($protocol, [423, 424, 425, 426, 427, 428], true)){
			return BedrockProtocolInfo::PROTOCOL_1_16_210;
		}
		return $protocol;
	}
}
